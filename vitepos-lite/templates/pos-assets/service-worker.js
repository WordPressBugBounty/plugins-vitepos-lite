if(!self.define){let s,e={};const c=(c,r)=>(c=new URL(c+".js",r).href,e[c]||new Promise((e=>{if("document"in self){const s=document.createElement("script");s.src=c,s.onload=e,document.head.appendChild(s)}else s=c,importScripts(c),e()})).then((()=>{let s=e[c];if(!s)throw new Error(`Module ${c} didn’t register its module`);return s})));self.define=(r,i)=>{const a=s||("document"in self?document.currentScript.src:"")||location.href;if(e[a])return;let d={};const o=s=>c(s,a),f={module:{uri:a},exports:d,require:o};e[a]=Promise.all(r.map((s=>f[s]||o(s)))).then((s=>(i(...s),d)))}}define(["./workbox-6567b62a"],(function(s){"use strict";s.setCacheNameDetails({prefix:"vitepos"}),self.addEventListener("message",(s=>{s.data&&"SKIP_WAITING"===s.data.type&&self.skipWaiting()})),s.precacheAndRoute([{url:"DoubleRing.svg",revision:"f71749e58bee8066166069e4c188e495"},{url:"Rolling.svg",revision:"72908447508a1cc4bb0471bc9c56b8a4"},{url:"Spinner.svg",revision:"70ed3fd217a2da2fb400e589411d114c"},{url:"Subtract.svg",revision:"281d2742dd7e019140283e559889b497"},{url:"barcode_print.css",revision:"a6cefba162d09c513d0a5c885fc76a35"},{url:"barcode_print.scss",revision:"40fea1f8c277bd603d37a063cab16a2b"},{url:"cashdrawer_print.css",revision:"141db9bf7428087eee555abee7fd4c06"},{url:"cashdrawer_print.scss",revision:"613fccd8a5be1c362da84b3117c363a7"},{url:"css/_main_root.scss",revision:"4123a114868ed3f6d2cd3561bc892aa5"},{url:"css/_variable.scss",revision:"16304756dbff9d8cfc96f8509d3b5c05"},{url:"css/_variable_cyan.scss",revision:"22226ad20620a145ca4366c6240f4e75"},{url:"css/_variable_dark.scss",revision:"dfad486712cf5e40c11d7e1cfa41b3b3"},{url:"css/_variable_gray.scss",revision:"870853b367962bef0ff3eda7fc146342"},{url:"css/_variable_green.scss",revision:"45ef51340b9ea3782b5baa007328c555"},{url:"css/_variable_orange.scss",revision:"6906b1ddabe13993fefc9197b515bbd3"},{url:"css/_variable_pink.scss",revision:"65949324e1cce739d4b5d47252f58de5"},{url:"css/_variable_purple.scss",revision:"e3c6c6b9132257171938c1eef5f7f7e0"},{url:"css/_variable_red.scss",revision:"bbf6108e67fdd4cceb740d330920d8f1"},{url:"css/color-cyan.css",revision:"ec6d76df7c0e776e5935164bbd09cd55"},{url:"css/color-cyan.scss",revision:"65083d47d40db7ed2fc4aa49d3b1fb6b"},{url:"css/color-dark.css",revision:"eda02e545e357864e7dfe1b2e417e36a"},{url:"css/color-dark.scss",revision:"f55fb7e9d43fc00e0d65d1174d48c554"},{url:"css/color-default.css",revision:"852f4227f31b7dff115d37d2fa43502a"},{url:"css/color-default.scss",revision:"9ac30e4651bff21dec6b21ad79892bbd"},{url:"css/color-gray.css",revision:"4bbf8a4cd66c762a131e83d02900b703"},{url:"css/color-gray.scss",revision:"5b76cb7e100e61126f2262b8de3a8c17"},{url:"css/color-green.css",revision:"176c2ff4f748a6558550471e1d517692"},{url:"css/color-green.scss",revision:"3c14665c1cfae82977c0b6e6ed43ebf2"},{url:"css/color-orange.css",revision:"9e76fe92339352957aa2d223548dc710"},{url:"css/color-orange.scss",revision:"56d0762d685bda1b2cb54c7040eb547a"},{url:"css/color-pink.css",revision:"a6eefcb8aeaaf4764173002118991700"},{url:"css/color-pink.scss",revision:"03259cb822e4c552fb7ef051131d43ea"},{url:"css/color-purple.css",revision:"e3f1f08c573490e5ae6e0c60bfd7e30a"},{url:"css/color-purple.scss",revision:"b113f822f0386ff5bffea0fe2f501b7e"},{url:"css/color-red.css",revision:"0b9ad93bceb643835768868d4c44162c"},{url:"css/color-red.scss",revision:"e94972d4462d6b177148b2527e7fd78e"},{url:"css/vitepos.css",revision:"6eb6f1f47e301743b19c9861248c1498"},{url:"custom-script.js",revision:"5bcd21817da93ba9946134a36cc18ff2"},{url:"error_tone.mp3",revision:"d2fa2a1496a56b6179e8fc1aed9237ad"},{url:"favicon.png",revision:"1a3320dd0e81d67001cea3dbbfb22c1d"},{url:"filename.png",revision:"4f0f4863a284eba8b32ea33779dab7a3"},{url:"font.css",revision:"7dd1a4d890fe439ce3b7830e40fcdfa5"},{url:"font.scss",revision:"85247adcd7d712695dfda86dc5c8d6b1"},{url:"fonts/Inter-Regular.ttf",revision:"eba360005eef21ac6807e45dc8422042"},{url:"fonts/vps.eot",revision:"aa9dbbbdaae804c23245af420770a31e"},{url:"fonts/vps.svg",revision:"99b7d7e53ed711e6c862dbd12131e3ef"},{url:"fonts/vps.ttf",revision:"a877676bb6478dca9d8150295d0301f7"},{url:"fonts/vps.woff",revision:"fc9e77f6aeacc897be581cac1c8e740d"},{url:"index.html",revision:"4c36195d6d9bbb8a277df982f1941920"},{url:"js/about.js",revision:"85d1b81037a3834c395b67fe1aa4d25c"},{url:"loader.svg",revision:"01e1455279765848c402fe2ac3695464"},{url:"logo.png",revision:"afa141175fc023babf5766fd8c8b0aef"},{url:"mackbook.png",revision:"4436b19e69895101fea9d1ca2b932153"},{url:"manifest.json",revision:"d151b5f6e310a70b2f270f10bac67466"},{url:"middle-button.svg",revision:"2aa8cbf81a1a2a15eba7f6e6cde3f60c"},{url:"no-img.svg",revision:"8f3032dc1c9e511da135584d77604df7"},{url:"pos-skins/black.png",revision:"8af6f5b9853d8e4abd0fb560cc9dff12"},{url:"pos-skins/cyan.png",revision:"5999cfba6ad42a3c5bb5161beacd5d91"},{url:"pos-skins/default.png",revision:"dde5727b983ab145c1f359760836efe9"},{url:"pos-skins/gray.png",revision:"723996cf91a66cbffd316114a2d80b55"},{url:"pos-skins/green.png",revision:"26a96fc0af83254f01c4d79dc8d52270"},{url:"pos-skins/orange.png",revision:"eb3f4e79f86bc3cb348372dcb886bbcf"},{url:"pos-skins/pink.png",revision:"8a8a214539be5f0fbef4b57e5c00d9d9"},{url:"pos-skins/purple.png",revision:"0f53b9e56cf2f702a712d822ce1db04b"},{url:"pos-skins/red.png",revision:"6e33f73d42bb82d2a59a1f45c783609c"},{url:"print.css",revision:"e3e51248fc1c15f8f550d6076246611d"},{url:"print.scss",revision:"304b05d7e35712b921e92d6122738c73"},{url:"robots.txt",revision:"735ab4f94fbcd57074377afca324c813"},{url:"success_tone.mp3",revision:"10ea902f885ac991b301fd4618efefd0"}],{})}));
