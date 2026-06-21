<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>User Profile</title>
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; margin: 0; padding: 20px; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .container { background: #111; padding: 25px; border-radius: 15px; border: 1px solid gold; width: 100%; max-width: 400px; text-align: center; }
        h2 { color: gold; margin-bottom: 20px; font-size: 20px; }
        .info-group { text-align: left; margin-bottom: 12px; border-bottom: 1px solid #333; padding-bottom: 5px; }
        .info-group strong { color: gold; display: block; font-size: 12px; }
        input { width: 100%; padding: 12px; margin: 8px 0; border-radius: 8px; border: 1px solid #444; background: #222; color: #fff; box-sizing: border-box; }
        button { width: 100%; padding: 12px; border: none; border-radius: 8px; margin-top: 10px; cursor: pointer; font-weight: bold; }
        .btn-edit { background: gold; color: #000; }
        .btn-save { background: #25D366; color: #fff; }
        .btn-close { background: #444; color: #fff; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h2>আমার প্রোফাইল</h2>
    
    <div id="viewMode">
        <div class="info-group"><strong>নাম</strong><span id="vName">লোড হচ্ছে...</span></div>
        <div class="info-group"><strong>বাবা</strong><span id="vFname"></span></div>
        <div class="info-group"><strong>মা</strong><span id="vMname"></span></div>
        <div class="info-group"><strong>ঠিকানা</strong><span id="vAddr"></span></div>
        <div class="info-group"><strong>মোবাইল</strong><span id="vMobile"></span></div>
        <div class="info-group"><strong>ইমেইল</strong><span id="vEmail"></span></div>
        <button class="btn-edit" onclick="toggleEdit(true)">এডিট করুন</button>
    </div>

    <div id="editMode" style="display:none;">
        <input type="text" id="name" placeholder="নাম">
        <input type="text" id="fname" placeholder="বাবার নাম">
        <input type="text" id="mname" placeholder="মায়ের নাম">
        <input type="text" id="addr" placeholder="ঠিকানা">
        <input type="text" id="mobile" placeholder="মোবাইল">
        <input type="text" id="email" placeholder="ইমেইল">
        <button class="btn-save" id="saveBtn" onclick="saveProfile()">সেভ করুন</button>
        <button class="btn-close" onclick="toggleEdit(false)">বাতিল করুন</button>
    </div>

    <button class="btn-close" onclick="closeProfileWindow()">বন্ধ করুন</button>
</div>

<script>
    function loadProfile() {
        const deviceId = window.parent.myDeviceId;
        if(!deviceId) return;
        fetch('channels_api.php?get_profile=true&did=' + encodeURIComponent(deviceId), {cache: 'no-store'})
            .then(res => res.json())
            .then(p => {
                document.getElementById('vName').innerText = p.name || 'N/A';
                document.getElementById('vFname').innerText = p.fname || 'N/A';
                document.getElementById('vMname').innerText = p.mname || 'N/A';
                document.getElementById('vAddr').innerText = p.addr || 'N/A';
                document.getElementById('vMobile').innerText = p.mobile || 'N/A';
                document.getElementById('vEmail').innerText = p.email || 'N/A';
            });
    }

    function toggleEdit(show) {
        if(show) {
            document.getElementById('name').value = document.getElementById('vName').innerText === 'N/A' ? '' : document.getElementById('vName').innerText;
            document.getElementById('fname').value = document.getElementById('vFname').innerText === 'N/A' ? '' : document.getElementById('vFname').innerText;
            document.getElementById('mname').value = document.getElementById('vMname').innerText === 'N/A' ? '' : document.getElementById('vMname').innerText;
            document.getElementById('addr').value = document.getElementById('vAddr').innerText === 'N/A' ? '' : document.getElementById('vAddr').innerText;
            document.getElementById('mobile').value = document.getElementById('vMobile').innerText === 'N/A' ? '' : document.getElementById('vMobile').innerText;
            document.getElementById('email').value = document.getElementById('vEmail').innerText === 'N/A' ? '' : document.getElementById('vEmail').innerText;
        }
        document.getElementById('viewMode').style.display = show ? 'none' : 'block';
        document.getElementById('editMode').style.display = show ? 'block' : 'none';
    }

    function saveProfile() {
        const btn = document.getElementById('saveBtn');
        btn.innerText = "সেভ হচ্ছে...";
        btn.disabled = true;
        
        const data = new FormData();
        data.append('did', window.parent.myDeviceId);
        data.append('name', document.getElementById('name').value);
        data.append('fname', document.getElementById('fname').value);
        data.append('mname', document.getElementById('mname').value);
        data.append('addr', document.getElementById('addr').value);
        data.append('mobile', document.getElementById('mobile').value);
        data.append('email', document.getElementById('email').value);

        fetch('channels_api.php', { method: 'POST', body: data })
        .then(res => res.json())
        .then(res => {
            btn.innerText = "সেভ করুন";
            btn.disabled = false;
            if(res.status === "success") {
                alert('সফলভাবে আপডেট হয়েছে!');
                loadProfile();
                toggleEdit(false);
            } else {
                alert('ব্যর্থ: ' + (res.db_error || 'Unknown Error'));
            }
        }).catch(() => {
            btn.innerText = "সেভ করুন";
            btn.disabled = false;
            alert('সার্ভার এরর!');
        });
    }

    function closeProfileWindow() { window.parent.closeProfile(); }
    window.onload = loadProfile;
</script>
</body>
</html>
