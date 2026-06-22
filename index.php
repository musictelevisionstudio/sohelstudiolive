<!DOCTYPE html>
<html lang="en">
<head>
<meta name="google-site-verification" content="DZCpf0nB8W2BBNFKLXpo5hSOTT1oHWVPLhxh4jNUIf8" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Sohel Free TV</title>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script src="https://www.youtube.com/iframe_api"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box;user-select:none;-webkit-tap-highlight-color:transparent;}
html,body{width:100%;height:100%;overflow:hidden;background:#000;font-family:sans-serif;}
.ctrl-btn:focus,.channel-item:focus{border:2px solid gold!important;outline:none;}
.video-wrap{position:fixed;inset:0;background:#000;z-index:1;}
#videoPlayer,#youtubeFrame{position:absolute;top:0;left:0;width:100%;height:100%;border:none;z-index:1;}
#adOverlay{position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.95);display:none;justify-content:center;align-items:center;}
#adFrame{width:90%;height:70%;border:2px solid gold;}
.ad-timer{position:absolute;top:20px;right:20px;color:gold;font-size:20px;font-weight:bold;}
#profileFrame,#supportFrame{position:fixed;top:0;left:0;width:100%;height:100%;z-index:20000;background:#000;display:none;border:none;}
#volIndicator{position:absolute;bottom:105px;left:50%;transform:translateX(-50%);z-index:9999;background:rgba(0,0,0,.8);color:gold;padding:4px 10px;border-radius:5px;font-size:12px;font-weight:bold;display:none;}
.bottom-info{position:absolute;bottom:0;left:0;width:100%;height:40px;background:#000;display:flex;align-items:center;z-index:500;}
.live-box{width:70px;height:100%;background:red;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;}
.ticker-wrap{flex:1;overflow:hidden;padding:0 10px;background:#000;}
.ticker{white-space:nowrap;color:#fff;font-size:16px;display:inline-block;padding-left:100%;animation:scroll-left linear infinite;}
@keyframes scroll-left{0%{transform:translateX(0%);}100%{transform:translateX(-100%);}}
.clock-box{width:140px;height:100%;background:#1565d8;color:#fff;display:flex;flex-direction:column;justify-content:center;align-items:center;font-size:11px;}
.ctrl-left{position:absolute;top:15%;left:10px;z-index:9999;display:flex;flex-direction:column;gap:10px;}
.ctrl-right{position:absolute;top:15%;right:10px;z-index:9999;display:flex;flex-direction:column;gap:10px;}
.ctrl-btn{background:#222;color:#fff;border:1px solid #444;padding:10px;border-radius:5px;cursor:pointer;font-size:11px;font-weight:bold;}
#channelDisplay{position:absolute;top:20px;left:50%;transform:translateX(-50%);z-index:9999;color:#fff;font-size:18px;background:rgba(0,0,0,0.7);padding:10px;border-radius:5px;display:none;text-align:center;pointer-events:none;}
.side-menu{position:absolute;top:0;right:-100%;width:250px;height:100%;background:#1a1a1a;z-index:10000;transition:.3s;overflow-y:auto;}
.side-menu.active{right:0;}
.channel-item{padding:15px;border-bottom:1px solid #333;color:#fff;cursor:pointer;}
</style>
</head>
<body>
<iframe id="profileFrame" src="profile.php"></iframe>
<iframe id="supportFrame" src="support.php"></iframe>
<div id="channelDisplay"></div>
<div id="volIndicator">VOL: 50%</div>
<div id="adOverlay"><div class="ad-timer" id="adTimer">Ad ends in: --</div><iframe id="adFrame" src="" allow="autoplay; fullscreen"></iframe></div>
<div class="video-wrap"><video id="videoPlayer" autoplay playsinline webkit-playsinline></video><iframe id="youtubeFrame" allow="autoplay; fullscreen" style="display:none;"></iframe></div>
<div class="ctrl-left">
<button class="ctrl-btn" tabindex="0" onclick="document.getElementById('profileFrame').style.display='block'">PROFILE</button>
<button class="ctrl-btn" tabindex="0" onclick="document.getElementById('supportFrame').style.display='block'">SUPPORT</button>
<button class="ctrl-btn" tabindex="0" onclick="prevChannel()">PREV</button>
<button class="ctrl-btn" tabindex="0" onclick="adjustVol(-0.05)">VOL-</button>
</div>
<div class="ctrl-right">
<button class="ctrl-btn" tabindex="0" onclick="adjustVol(0.05)">VOL+</button>
<button class="ctrl-btn" tabindex="0" onclick="nextChannel()">NEXT</button>
<button class="ctrl-btn" tabindex="0" onclick="toggleFS()">FULL</button>
<button class="ctrl-btn" tabindex="0" onclick="toggleMenu()">MENU</button>
</div>
<div class="bottom-info"><div class="live-box" id="liveBtn">LIVE</div><div class="ticker-wrap"><div class="ticker" id="headline">Welcome!</div></div><div class="clock-box"><div id="clock">00:00:00</div><div id="date">00/00/00</div></div></div>
<div id="sideMenu" class="side-menu"><div style="padding:15px;background:#1565d8;color:#fff;text-align:center;font-weight:bold;display:flex;justify-content:space-between;align-items:center;">CHANNELS <button onclick="toggleMenu()" style="background:red;color:#fff;border:none;padding:5px 10px;cursor:pointer;">CLOSE</button></div><div id="channelList"></div></div>
<script>
document.addEventListener('keydown',function(e){if(e.keyCode===13){document.activeElement.click();}});
let currentChannel=0,channels=[],hls=null;const video=document.getElementById('videoPlayer'),youtubeFrame=document.getElementById('youtubeFrame'),cd=document.getElementById('channelDisplay');
function closeProfile(){document.getElementById('profileFrame').style.display='none';}
function closeSupport(){document.getElementById('supportFrame').style.display='none';}
function showNameAuto(n){cd.innerText=n;cd.style.display='block';setTimeout(()=>{cd.style.display='none';},5000);}
function getDeviceId(){let d=localStorage.getItem('unique_device_id');if(!d){d='DEV-'+Date.now()+Math.floor(Math.random()*1000);localStorage.setItem('unique_device_id',d);setDefaultProfile();}return d;}
const myDeviceId=getDeviceId();window.myDeviceId=myDeviceId;
function setDefaultProfile(){const d={name:"Default User",fname:"N/A",mname:"N/A",addr:"Not Provided",mobile:"01XXXXXXXXX",email:"user@example.com"};localStorage.setItem('user_profile',JSON.stringify(d));}
function showActivationForm(){const s=JSON.parse(localStorage.getItem('user_profile'))||{};const o=document.createElement('div');o.id="formOverlay";o.style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.95);z-index:999999;display:flex;justify-content:center;align-items:center;padding:20px;";o.innerHTML=`<div style="background:#1a1a1a;padding:20px;border-radius:15px;width:100%;max-width:400px;color:#fff;border:1px solid #444;"><h3 style="text-align:center;margin-bottom:15px;color:#1565d8;">অ্যাক্টিভেশন তথ্য</h3><input type="text" id="name" value="${s.name||''}" placeholder="আপনার নাম" style="width:100%;padding:10px;margin:5px 0;"><input type="text" id="fname" value="${s.fname||''}" placeholder="বাবার নাম" style="width:100%;padding:10px;margin:5px 0;"><input type="text" id="mname" value="${s.mname||''}" placeholder="মায়ের নাম" style="width:100%;padding:10px;margin:5px 0;"><input type="text" id="addr" value="${s.addr||''}" placeholder="ঠিকানা" style="width:100%;padding:10px;margin:5px 0;"><input type="text" id="mobile" value="${s.mobile||''}" placeholder="মোবাইল নাম্বার" style="width:100%;padding:10px;margin:5px 0;"><input type="email" id="email" value="${s.email||''}" placeholder="ইমেইল আইডি" style="width:100%;padding:10px;margin:5px 0;"><button id="submitBtn" style="width:100%;padding:12px;background:#25D366;color:#fff;border:none;margin-top:10px;">সাবমিট করুন</button><button onclick="document.getElementById('formOverlay').remove()" style="width:100%;padding:10px;background:red;color:#fff;border:none;margin-top:10px;">বন্ধ করুন</button></div>`;document.body.appendChild(o);document.getElementById('submitBtn').onclick=function(){const p={name:document.getElementById('name').value,fname:document.getElementById('fname').value,mname:document.getElementById('mname').value,addr:document.getElementById('addr').value,mobile:document.getElementById('mobile').value,email:document.getElementById('email').value};localStorage.setItem('user_profile',JSON.stringify(p));const m="নাম: "+p.name+"\nবাবা: "+p.fname+"\nমা: "+p.mname+"\nঠিকানা: "+p.addr+"\nমোবাইল: "+p.mobile+"\nইমেইল: "+p.email+"\nDevice ID: "+myDeviceId;window.location.href="https://wa.me/8801615896688?text="+encodeURIComponent(m);};}

// আপডেট করা fetchData ফাংশন
async function fetchData(){
    try{
        const r=await fetch('channels_api.php?did='+encodeURIComponent(myDeviceId),{cache:"no-cache"});
        const d=await r.json();
        if(d.status==="inactive"){
            document.body.innerHTML=`<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;background:#000;color:#fff;padding:20px;text-align:center;"><div style="background:#222;padding:15px;border-radius:10px;margin-bottom:20px;width:100%;max-width:400px;">সফটওয়্যারটি লক করা আছে!</div><h2 style="color:#00ff00;">${myDeviceId}</h2><button onclick="showActivationForm()" style="padding:15px;background:#1565d8;color:#fff;border:none;margin-top:20px;">যোগাযোগ</button></div>`;
            return;
        }
        channels=d.channels;
        renderList();
        if(channels.length>0){
            let l=parseInt(localStorage.getItem('last_channel')||0);
            if(l>=channels.length) l=0;
            playChannel(l);
        }
    }catch(e){console.error("API Connection Error");}
}

function playChannel(i){currentChannel=i;localStorage.setItem('last_channel',i);const c=channels[i];if(!c)return;if(c.ads_status==1&&c.ad_url)showAd(c.ad_url,c.ad_duration||30);const h=document.getElementById('headline');h.innerText=c.ticker_text||"Welcome!";h.style.animationDuration=(c.ticker_speed||40)+'s';document.getElementById('liveBtn').innerText=c.live_button_text||"LIVE";showNameAuto(c.name);const isY=c.url.includes('youtube.com')||c.url.includes('youtu.be');if(isY){video.style.display='none';youtubeFrame.style.display='block';if(window.currentHls)window.currentHls.destroy();let v=c.url.split('v=')[1]||c.url.split('/').pop();if(v.includes('?'))v=v.split('?')[0];youtubeFrame.src="https://www.youtube.com/embed/"+v+"?autoplay=1&rel=0&controls=0&mute=0";}else{youtubeFrame.style.display='none';video.style.display='block';if(window.currentHls)window.currentHls.destroy();if(Hls.isSupported()){window.currentHls=new Hls({lowLatencyMode:true,maxBufferLength:5});window.currentHls.loadSource(c.url);window.currentHls.attachMedia(video);window.currentHls.on(Hls.Events.MANIFEST_PARSED,()=>video.play().catch(()=>{}));}else{video.src=c.url;video.play().catch(()=>{});}}}
function showAd(u,d){const o=document.getElementById('adOverlay'),f=document.getElementById('adFrame'),t=document.getElementById('adTimer');let v=u.split('v=')[1]||u.split('/').pop();if(v.includes('?'))v=v.split('?')[0];f.src="https://www.youtube.com/embed/"+v+"?autoplay=1&rel=0&controls=0";o.style.display='flex';let l=d;const i=setInterval(()=>{t.innerText="Ad ends in: "+l+"s";if(l<=0){clearInterval(i);o.style.display='none';f.src="";}l--;},1000);}
function adjustVol(v){video.muted=false;video.volume=Math.min(Math.max(video.volume+v,0),1);document.getElementById('volIndicator').style.display='block';document.getElementById('volIndicator').innerText="VOL: "+Math.round(video.volume*100)+"%";clearTimeout(window.volTimeout);window.volTimeout=setTimeout(()=>{document.getElementById('volIndicator').style.display='none';},1000);}
function toggleFS(){if(!document.fullscreenElement)document.documentElement.requestFullscreen().catch(()=>{});else document.exitFullscreen().catch(()=>{});}
function toggleMenu(){document.getElementById('sideMenu').classList.toggle('active');}
function nextChannel(){currentChannel=(currentChannel+1)%channels.length;playChannel(currentChannel);}
function prevChannel(){currentChannel=(currentChannel-1+channels.length)%channels.length;playChannel(currentChannel);}
function renderList(){const l=document.getElementById('channelList');l.innerHTML="";channels.forEach((c,i)=>{const d=document.createElement('div');d.className='channel-item';d.tabIndex=0;d.innerText=c.name;d.onclick=()=>{playChannel(i);toggleMenu();};l.appendChild(d);});}
setInterval(()=>{const d=new Date();document.getElementById('clock').innerText=d.toLocaleTimeString('en-US',{timeZone:'Asia/Dhaka',hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:true});document.getElementById('date').innerText=d.toLocaleDateString('en-US',{timeZone:'Asia/Dhaka',weekday:'short',month:'short',day:'2-digit',year:'2-digit'});},1000);window.onload=fetchData;
</script>
</body>
</html>