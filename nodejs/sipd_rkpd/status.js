const env=require('dotenv').config({path: __dirname+'/../../.env'})
var request = require('request');
const { Client } = require('pg');
const fs = require('fs');
var tahun=0;
const client = new Client({
  host: env.parsed.DB_HOST,
  port: env.parsed.DB_PORT,
  user: env.parsed.DB_USERNAME,
  password:env.parsed.DB_PASSWORD,
  database:"production_sinkron",
  query_timeout:1000000,
  statement_timeout:1000000
});
var width=1300;
var height=750;
var args=[];
args.push(`--window-size=${width},${height}`)
args.push('--no-sandbox')


const puppeteer = require('puppeteer');
tahun=process.argv[process.argv.length -1];
console.log(tahun);


async function login(){
	try{


	const browser =  await puppeteer.launch({headless: false,ignoreHTTPSErrors: true,defaultViewport:null,
      args: ['--no-sandbox']
  });

	const page = await browser.newPage();
    await page.setDefaultNavigationTimeout(50000);
    await page.goto('https://sipd.kemendagri.go.id/run/b9d94ba3be22c14dc2413b0abeedbf8df2f53819/?m=dashboard&app=rkpd',{ waitUntil: 'networkidle0' });
    await page.type('#userX','subpkp@bangda.kemendagri.go.id');
    await page.type('#passX',('bangdapkp'));
    await page.click('[name="tahun"][value="'+tahun+'"]');
     const result = await page.evaluate((daerah)=>{
              return new Promise((resove,reject)=>{
                $('input').trigger('change');
                  resove(1);

              });
     });

    await Promise.all([
          page.click('button[type="submit"]')
          ,page.waitForNavigation({ waitUntil: 'networkidle0' })
    ]);

     console.log('lagin berhasil');

      getData(page);
   	

	}catch(e){
    console.log(e);
    process.exit(0);

	}
}


async function getData(page){
     console.log('mengambil data');

    const result = await page.evaluate(()=>{
          return new Promise((resove,reject)=>{
              console.log('myscript injected');
              var damp_path=window.location.href;
              var damp_regex=/run.[/]?.*[/]/;
              var damp_time=new Date().getTime()+'';
              var damp_key= damp_regex.exec(damp_path)[0];
              damp_path=damp_path.split(damp_key)[0];
              damp_path=damp_path+damp_key+'?m=pusat_rkpd_dashboard&f=ajax_list_pemda&tipe=murni&_='+damp_time;

              console.log('unique '+damp_key);
              setTimeout(function(){
                $.get(damp_path,function(res){
                 resove(JSON.parse(res));
                });
        },1000);
              



         });
     });

    var data_daerah={
      updated_at:new Date(),
      data:result.data
    };
     console.log(data_daerah);



    await fs.writeFileSync(__dirname+'/storage/file-status-rkpd-'+tahun+'.json',JSON.stringify(data_daerah,undefined,4));
     
    process.exit(0);

}


setTimeout(function(){
	login();
},100);