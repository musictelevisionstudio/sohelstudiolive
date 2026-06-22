<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sohel Free TV</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script src="https://www.youtube.com/iframe_api"></script>
    <style>
        *{ margin:0; padding:0; box-sizing:border-box; user-select:none; -webkit-tap-highlight-color: transparent; }
        html, body { width:100%; height:100%; overflow:hidden; background:#000; font-family:sans-serif; }
        
        .ctrl-btn:focus, .channel-item:focus { border: 2px solid gold !important; outline: none; }
        
        .video-wrap{ position:fixed; inset:0; background:#000; z-index:1; }
        #videoPlayer, #youtubeFrame { position:absolute; top:0; left:0; width:100%; height:100%; border:none; z-index: 1; }
        #adOverlay { position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,0.95); display:none; justify-content:center; align-items:center; }
        #adFrame { width:90%; height:70%; border:2px solid gold; }
        .ad-timer { position:absolute; top:20px; right:20px; color:gold; font-size:20px; font-weight:bold; }
        #profileFrame, #supportFrame { position:fixed; top:0; left:0; width:100%; height:100%; z-index:20000; background:#000; display:none; border:none; }
        #volIndicator { position:absolute; bottom:105px; left:50%; transform:translateX(-50%); z-index:9999; background:rgba(0,0,0,.8); color:gold; padding:4px 10px; border-radius:5px; font-size:12px; font-weight:bold; display:none; }
        .bottom-info{ position:absolute; bottom:0; left:0; width:100%; height:40px; background:#000; display:flex; align-items:center; z-index:500; }
        .live-box{ width:70px; height:100%; background:red; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:14px; }
        .ticker-wrap{ flex:1; overflow:hidden; padding:0 10px; background: #000; }
        .ticker{ white-space:nowrap; color:#fff; font-size:16px; display:inline-block; padding-left: 100%; animation: scroll-left linear infinite; }
        @keyframes scroll-left{ 0%{transform:translateX(0%);} 100%{transform:translateX(-100%);} }
        .clock-box{ width:140px; height:100%; background:#1565d8; color:#fff; display:flex; flex-direction:column; justify-content:center; align-items:center; font-size:11px; }
        .ctrl-left { position:absolute; top: 15%; left: 10px; z-index: 9999; display:flex; flex-direction:column; gap: 10px; }
        .ctrl-right { position:absolute; top: 15%; right: 10px; z-index: 9999; display:flex; flex-direction:column; gap: 10px; }
        .ctrl-btn { background:#222; color:#fff; border:1px solid #444; padding:10px; border-radius:5px; cursor:pointer; font-size:11px; font-weight:bold; }
        #channelDisplay { position:absolute; top:20px; left:50%; transform:translateX(-50%); z-index:9999; color:#fff; font-size:18px; background:rgba(0,0,0,0.7); padding:10px; border-radius:5px; display:none; text-align:center; pointer-events:none; }
        .side-menu{ position:absolute; top:0; right:-100%; width:250px; height:100%; background:#1a1a1a; z-index:10000; transition:.3s; overflow-y:auto; }
        .side-menu.active{ right:0; }
        .channel-item{ padding:15px; border-bottom:1px solid #333; color:#fff; cursor:pointer; }
    </style>
</head>
<body>

<iframe id="profileFrame" src="profile.php"></iframe>
<iframe id="supportFrame" src="support.php"></iframe>
<div id="channelDisplay"></div>
<div id="volIndicator">VOL: 50%</div>

<div id="adOverlay">
    <div class="ad-timer" id="adTimer">Ad ends in: --</div>
    <iframe id="adFrame" src="" allow="autoplay; fullscreen"></iframe>
</div>

<div class="video-wrap">
    <video id="videoPlayer" autoplay playsinline webkit-playsinline></video>
    <iframe id="youtubeFrame" allow="autoplay; fullscreen" style="display:none;"></iframe>
</div>

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

<div class="bottom-info">
    <div class="live-box" id="liveBtn">LIVE</div>
    <div class="ticker-wrap"><div class="ticker" id="headline">Welcome!</div></div>
    <div class="clock-box"><div id="clock">00:00:00</div><div id="date">00/00/00</div></div>
</div>

<div id="sideMenu" class="side-menu">
    <div style="padding:15px; background:#1565d8; color:#fff; text-align:center; font-weight:bold; display:flex; justify-content:space-between; align-items:center;">CHANNELS <button onclick="toggleMenu()" style="background:red;color:#fff;border:none;padding:5px 10px;cursor:pointer;">CLOSE</button></div>
    <div id="channelList"></div>
</div>

<script>
    document.addEventListener('keydown', function(e) {
        if(e.keyCode === 13) { document.activeElement.click(); }
    });

    let currentChannel = 0, channels = [], hls = null;
    const video = document.getElementById('videoPlayer');
    const youtubeFrame = document.getElementById('youtubeFrame');
    const cd = document.getElementById('channelDisplay');

    function closeProfile() { document.getElementById('profileFrame').style.display = 'none'; }
    function closeSupport() { document.getElementById('supportFrame').style.display = 'none'; }
    
    function showNameAuto(name) { cd.innerText = name; cd.style.display = 'block'; setTimeout(() => { cd.style.display = 'none'; }, 5000); }

    function getDeviceId() {
        let deviceId = localStorage.getItem('unique_device_id');
        if (!deviceId) { deviceId = 'DEV-' + Date.now() + Math.floor(Math.random() * 1000); localStorage.setItem('unique_device_id', deviceId); setDefaultProfile(); }
        return deviceId;
    }
    const myDeviceId = getDeviceId();
    window.myDeviceId = myDeviceId;

    function setDefaultProfile() {
        const defaultData = { name: "Default User", fname: "N/A", mname: "N/A", addr: "Not Provided", mobile: "01XXXXXXXXX", email: "user@example.com" };
        localStorage.setItem('user_profile', JSON.stringify(defaultData));
    }

    function showActivationForm() {
        const storedProfile = JSON.parse(localStorage.getItem('user_profile')) || {};
        const overlay = document.createElement('div');
        overlay.id = "formOverlay";
        overlay.style = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.95); z-index:999999; display:flex; justify-content:center; align-items:center; padding:20px;";
        overlay.innerHTML = `<div style="background:#1a1a1a; padding:20px; border-radius:15px; width:100%; max-width:400px; color:#fff; border:1px solid #444;">
                <h3 style="text-align:center; margin-bottom:15px; color:#1565d8;">অ্যাক্টিভেশন তথ্য</h3>
                <input type="text" id="name" value="${storedProfile.name || ''}" placeholder="আপনার নাম" style="width:100%; padding:10px; margin:5px 0; border-radius:5px; border:none;">
                <input type="text" id="fname" value="${storedProfile.fname || ''}" placeholder="বাবার নাম" style="width:100%; padding:10px; margin:5px 0; border-radius:5px; border:none;">
                <input type="text" id="mname" value="${storedProfile.mname || ''}" placeholder="মায়ের নাম" style="width:100%; padding:10px; margin:5px 0; border-radius:5px; border:none;">
                <input type="text" id="addr" value="${storedProfile.addr || ''}" placeholder="ঠিকানা" style="width:100%; padding:10px; margin:5px 0; border-radius:5px; border:none;">
                <input type="text" id="mobile" value="${storedProfile.mobile || ''}" placeholder="মোবাইল নাম্বার" style="width:100%; padding:10px; margin:5px 0; border-radius:5px; border:none;">
                <input type="email" id="email" value="${storedProfile.email || ''}" placeholder="ইমেইল আইডি" style="width:100%; padding:10px; margin:5px 0; border-radius:5px; border:none;">
                <button id="submitBtn" style="width:100%; padding:12px; background:#25D366; color:#fff; border:none; border-radius:5px; margin-top:15px; cursor:pointer; font-weight:bold;">সাবমিট করুন</button>
                <button onclick="document.getElementById('formOverlay').remove()" style="width:100%; padding:10px; background:red; color:#fff; border:none; border-radius:5px; margin-top:10px; cursor:pointer;">বন্ধ করুন</button>
            </div>`;
        document.body.appendChild(overlay);
        document.getElementById('submitBtn').onclick = function() {
            const profileData = { name: document.getElementById('name').value, fname: document.getElementById('fname').value, mname: document.getElementById('mname').value, addr: document.getElementById('addr').value, mobile: document.getElementById('mobile').value, email: document.getElementById('email').value };
            localStorage.setItem('user_profile', JSON.stringify(profileData));
            const msg = "নাম: " + profileData.name + "\nবাবা: " + profileData.fname + "\nমা: " + profileData.mname + "\nঠিকানা: " + profileData.addr + "\nমোবাইল: " + profileData.mobile + "\nইমেইল: " + profileData.email + "\nDevice ID: " + myDeviceId;
            window.location.href = "https://wa.me/8801615896688?text=" + encodeURIComponent(msg);
        };
    }

    async function fetchData(){
        try {
            const res = await fetch('channels_api.php?did=' + myDeviceId, {cache: "no-cache"});
            const data = await res.json();
            if(data.status === "inactive") {
                document.body.innerHTML = `<div style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; background:#000; color:#fff; padding:20px; text-align:center;">
                    <div style="background:#222; padding:15px; border-radius:10px; margin-bottom:20px; width:100%; max-width:400px; color:#ddd; font-size:18px;">সফটওয়্যারটি লক করা আছে!</div>
                    <div style="background:#222; padding:20px; border-radius:15px; border:2px solid #555; width:100%; max-width:400px; margin-bottom:15px;">
                        <p style="font-size:14px; color:#bbb;">আপনার ডিভাইস আইডি:</p>
                        <h2 style="color:#00ff00; word-break:break-all; margin-top:10px;">${myDeviceId}</h2>
                    </div>
                    <button onclick="showActivationForm()" style="width:100%; max-width:400px; padding:15px; background:#1565d8; color:#fff; border:none; border-radius:10px; font-weight:bold; font-size:16px; cursor:pointer;">যোগাযোগ</button>
                </div>`;
                return;
            }
            channels = data.channels;
            renderList();
            if(channels.length > 0){
                let lastChannel = parseInt(localStorage.getItem('last_channel') || 0);
                if(lastChannel >= channels.length){ lastChannel = 0; }
                playChannel(lastChannel);
            }
        } catch(e) { console.log("Fetch Error"); }
    }

    function playChannel(idx){
        currentChannel = idx;
        localStorage.setItem('last_channel', idx); 
        const ch = channels[idx];
        if(!ch) return;

        if(ch.ads_status == 1 && ch.ad_url) { showAd(ch.ad_url, ch.ad_duration || 30); }

        const headlineDiv = document.getElementById('headline');
        headlineDiv.innerText = ch.ticker_text || "Welcome!";
        headlineDiv.style.animationDuration = (ch.ticker_speed || 40) + 's';
        document.getElementById('liveBtn').innerText = ch.live_button_text || "LIVE";

        showNameAuto(ch.name);

        const isYouTube = ch.url.includes('youtube.com') || ch.url.includes('youtu.be');
        if(isYouTube){
            video.style.display = 'none'; youtubeFrame.style.display = 'block';
            if(window.currentHls){ window.currentHls.destroy(); }
            let vId = ch.url.split('v=')[1] || ch.url.split('/').pop();
            if(vId.includes('?')) vId = vId.split('?')[0];
            youtubeFrame.src = "https://www.youtube.com/embed/" + vId + "?autoplay=1&rel=0&controls=0&mute=0";
        } else {
            youtubeFrame.style.display = 'none'; video.style.display = 'block';
            if(window.currentHls){ window.currentHls.destroy(); }
            if(Hls.isSupported()){
                window.currentHls = new Hls({lowLatencyMode: true, maxBufferLength: 5});
                window.currentHls.loadSource(ch.url);
                window.currentHls.attachMedia(video);
                window.currentHls.on(Hls.Events.MANIFEST_PARSED, () => video.play().catch(()=>{}));
            } else { video.src = ch.url; video.play().catch(()=>{}); }
        }
    }

    function showAd(url, duration) {
        const overlay = document.getElementById('adOverlay');
        const frame = document.getElementById('adFrame');
        const timer = document.getElementById('adTimer');
        let vId = url.split('v=')[1] || url.split('/').pop();
        if(vId.includes('?')) vId = vId.split('?')[0];
        frame.src = "https://www.youtube.com/embed/" + vId + "?autoplay=1&rel=0&controls=0";
        overlay.style.display = 'flex';
        let timeLeft = duration;
        const interval = setInterval(() => {
            timer.innerText = "Ad ends in: " + timeLeft + "s";
            if(timeLeft <= 0) { clearInterval(interval); overlay.style.display = 'none'; frame.src = ""; }
            timeLeft--;
        }, 1000);
    }

    function rotateScreen() { if (screen.orientation && screen.orientation.lock) { screen.orientation.lock('landscape').catch(function(err) {}); } }
    function adjustVol(v){ 
        video.muted = false; let newVol = video.volume + v;
        video.volume = Math.min(Math.max(newVol, 0), 1);
        document.getElementById('volIndicator').style.display = 'block';
        document.getElementById('volIndicator').innerText = "VOL: " + Math.round(video.volume * 100) + "%";
        clearTimeout(window.volTimeout); window.volTimeout = setTimeout(() => { document.getElementById('volIndicator').style.display = 'none'; }, 1000);
    }
    function toggleFS() { if (!document.fullscreenElement) { document.documentElement.requestFullscreen().catch(() => {}); } else { document.exitFullscreen().catch(() => {}); } }
    function toggleMenu(){ document.getElementById('sideMenu').classList.toggle('active'); }
    function nextChannel(){ currentChannel = (currentChannel + 1) % channels.length; playChannel(currentChannel); }
    function prevChannel(){ currentChannel = (currentChannel - 1 + channels.length) % channels.length; playChannel(currentChannel); }
    
    function renderList(){ 
        const list = document.getElementById('channelList'); list.innerHTML = ""; 
        channels.forEach((ch, idx) => { 
            const div = document.createElement('div'); div.className = 'channel-item'; 
            div.tabIndex = 0; div.innerText = ch.name; 
            div.onclick = () => { playChannel(idx); toggleMenu(); }; list.appendChild(div); 
        }); 
    }
    
    setInterval(() => {
        const d = new Date();
        document.getElementById('clock').innerText = d.toLocaleTimeString('en-US', { timeZone: 'Asia/Dhaka', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
        document.getElementById('date').innerText = d.toLocaleDateString('en-US', { timeZone: 'Asia/Dhaka', weekday: 'short', month: 'short', day: '2-digit', year: '2-digit' });
    }, 1000);
    window.onload = fetchData;
</script>
</body>
</html