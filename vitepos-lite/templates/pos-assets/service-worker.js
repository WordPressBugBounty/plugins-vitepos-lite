if(!self.define){let s,e={};const c=(c,r)=>(c=new URL(c+".js",r).href,e[c]||new Promise((e=>{if("document"in self){const s=document.createElement("script");s.src=c,s.onload=e,document.head.appendChild(s)}else s=c,importScripts(c),e()})).then((()=>{let s=e[c];if(!s)throw new Error(`Module ${c} didn’t register its module`);return s})));self.define=(r,i)=>{const a=s||("document"in self?document.currentScript.src:"")||location.href;if(e[a])return;let f={};const o=s=>c(s,a),d={module:{uri:a},exports:f,require:o};e[a]=Promise.all(r.map((s=>d[s]||o(s)))).then((s=>(i(...s),f)))}}define(["./workbox-6567b62a"],(function(s){"use strict";s.setCacheNameDetails({prefix:"vitepos"}),self.addEventListener("message",(s=>{s.data&&"SKIP_WAITING"===s.data.type&&self.skipWaiting()})),s.precacheAndRoute([{url:"DoubleRing.svg",revision:"f71749e58bee8066166069e4c188e495"},{url:"Rolling.svg",revision:"72908447508a1cc4bb0471bc9c56b8a4"},{url:"Spinner.svg",revision:"70ed3fd217a2da2fb400e589411d114c"},{url:"Subtract.svg",revision:"281d2742dd7e019140283e559889b497"},{url:"addons/vite-coupon-banner.png",revision:"55c930d5f7a426828ea5f0b8d34b6611"},{url:"addons/vite-reward-banner.png",revision:"f9f19bd8191b2fa834e121dcdc20e34d"},{url:"barcode_print.css",revision:"b5b58ae9ddf1c9f19eed4ad67df32c33"},{url:"barcode_print.scss",revision:"c070c7ca63db0571716a27305c210ea8"},{url:"cashdrawer_print.css",revision:"ec70e52054adb77462ab710b093ddc60"},{url:"cashdrawer_print.scss",revision:"613fccd8a5be1c362da84b3117c363a7"},{url:"css/_main_root.scss",revision:"24054b503dad0eff69150122d978a64c"},{url:"css/_variable.scss",revision:"bcca1ec84fbe46101c05f0de37819f72"},{url:"css/_variable_cyan.scss",revision:"22226ad20620a145ca4366c6240f4e75"},{url:"css/_variable_dark.scss",revision:"dfad486712cf5e40c11d7e1cfa41b3b3"},{url:"css/_variable_gray.scss",revision:"870853b367962bef0ff3eda7fc146342"},{url:"css/_variable_green.scss",revision:"45ef51340b9ea3782b5baa007328c555"},{url:"css/_variable_orange.scss",revision:"6906b1ddabe13993fefc9197b515bbd3"},{url:"css/_variable_pink.scss",revision:"65949324e1cce739d4b5d47252f58de5"},{url:"css/_variable_purple.scss",revision:"e3c6c6b9132257171938c1eef5f7f7e0"},{url:"css/_variable_red.scss",revision:"bbf6108e67fdd4cceb740d330920d8f1"},{url:"css/color-cyan.css",revision:"b50a577a7940356e5f8d6da4dc76e6e6"},{url:"css/color-cyan.scss",revision:"65083d47d40db7ed2fc4aa49d3b1fb6b"},{url:"css/color-dark.css",revision:"3d26c5676f2350ff4385df7c98bbd0c9"},{url:"css/color-dark.scss",revision:"f55fb7e9d43fc00e0d65d1174d48c554"},{url:"css/color-default.css",revision:"83cc6021df19fc3fc203c1650043d5ec"},{url:"css/color-default.scss",revision:"9ac30e4651bff21dec6b21ad79892bbd"},{url:"css/color-gray.css",revision:"5c4c84e1dfeb54578b24a8cece07174b"},{url:"css/color-gray.scss",revision:"5b76cb7e100e61126f2262b8de3a8c17"},{url:"css/color-green.css",revision:"b60548d03a0324337844895c1e12b9e2"},{url:"css/color-green.scss",revision:"3c14665c1cfae82977c0b6e6ed43ebf2"},{url:"css/color-orange.css",revision:"d60368f34fe2445e17135afcb31352d3"},{url:"css/color-orange.scss",revision:"56d0762d685bda1b2cb54c7040eb547a"},{url:"css/color-pink.css",revision:"4f2c3a5b7b4b71307c47fe4054466844"},{url:"css/color-pink.scss",revision:"03259cb822e4c552fb7ef051131d43ea"},{url:"css/color-purple.css",revision:"97c361a27507255b98755a45d107d7c6"},{url:"css/color-purple.scss",revision:"b113f822f0386ff5bffea0fe2f501b7e"},{url:"css/color-red.css",revision:"f1240199ede6b78b4b1a575994fd3890"},{url:"css/color-red.scss",revision:"e94972d4462d6b177148b2527e7fd78e"},{url:"css/vitepos.css",revision:"62bf59a33525bfe35e61a81da86076ce"},{url:"custom-script.js",revision:"5bcd21817da93ba9946134a36cc18ff2"},{url:"error_tone.mp3",revision:"d2fa2a1496a56b6179e8fc1aed9237ad"},{url:"favicon.png",revision:"1a3320dd0e81d67001cea3dbbfb22c1d"},{url:"filename.png",revision:"4f0f4863a284eba8b32ea33779dab7a3"},{url:"font.css",revision:"9557c436d821af107819f03147280bb8"},{url:"font.scss",revision:"85247adcd7d712695dfda86dc5c8d6b1"},{url:"fonts/Inter-Regular.ttf",revision:"eba360005eef21ac6807e45dc8422042"},{url:"fonts/vps.eot",revision:"c098aea1cd3ef65fb5126850fcc77cdb"},{url:"fonts/vps.svg",revision:"619bcfcd936e76f87dd347e3ebf89019"},{url:"fonts/vps.ttf",revision:"d0f742232e02823e89e0801dd75b75be"},{url:"fonts/vps.woff",revision:"8d2e5450d180b38824842f1770609a13"},{url:"index.html",revision:"1ba64abff1729b179617b7f9faa57008"},{url:"js/about.js",revision:"e522246884331bc0bd06f2205eb1e5f5"},{url:"loader.svg",revision:"01e1455279765848c402fe2ac3695464"},{url:"logo.png",revision:"afa141175fc023babf5766fd8c8b0aef"},{url:"mackbook.png",revision:"4436b19e69895101fea9d1ca2b932153"},{url:"manifest.json",revision:"d151b5f6e310a70b2f270f10bac67466"},{url:"middle-button.svg",revision:"2aa8cbf81a1a2a15eba7f6e6cde3f60c"},{url:"no-img.svg",revision:"8f3032dc1c9e511da135584d77604df7"},{url:"pos-skins/black.png",revision:"8af6f5b9853d8e4abd0fb560cc9dff12"},{url:"pos-skins/cyan.png",revision:"5999cfba6ad42a3c5bb5161beacd5d91"},{url:"pos-skins/default.png",revision:"dde5727b983ab145c1f359760836efe9"},{url:"pos-skins/gray.png",revision:"723996cf91a66cbffd316114a2d80b55"},{url:"pos-skins/green.png",revision:"26a96fc0af83254f01c4d79dc8d52270"},{url:"pos-skins/orange.png",revision:"eb3f4e79f86bc3cb348372dcb886bbcf"},{url:"pos-skins/pink.png",revision:"8a8a214539be5f0fbef4b57e5c00d9d9"},{url:"pos-skins/purple.png",revision:"0f53b9e56cf2f702a712d822ce1db04b"},{url:"pos-skins/red.png",revision:"6e33f73d42bb82d2a59a1f45c783609c"},{url:"print.css",revision:"5c3ca2a35981060b927b5e1f64b7c6a9"},{url:"print.scss",revision:"a5b3d5f4b38b9368f013072613583fb5"},{url:"robots.txt",revision:"735ab4f94fbcd57074377afca324c813"},{url:"success_tone.mp3",revision:"10ea902f885ac991b301fd4618efefd0"}],{})}));
