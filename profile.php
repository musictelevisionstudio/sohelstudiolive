<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body { background: #121212; color: #e0e0e0; font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #1e1e1e; padding: 30px; border-radius: 15px; box-shadow: 0 8px 24px rgba(0,0,0,0.3); max-width: 400px; width: 100%; text-align: center; }
        h2 { color: #1565d8; margin-bottom: 20px; }
        .info-group { text-align: left; margin-bottom: 15px; border-bottom: 1px solid #333; padding-bottom: 5px; }
        .info-group strong { color: #888; display: block; font-size: 0.85em; }
        input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #444; background: #2a2a2a; color: #fff; box-sizing: border-box; }
        button { width: 100%; padding: 12px; border: none; border-radius: 8px; margin-top: 10px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-edit { background: #1565d8; color: #fff; }
        .btn-save { background: #25D366; color: #fff; }
        .btn-close { background: #444; color: #fff; margin-top: 15px; }
        button:hover { opacity: 0.9; }
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
        // আপনার API থেকে ডাটাবেসের কলাম অনুযায়ী ডাটা লোড হবে
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
        
        const data = new URLSearchParams();
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
                alert('তথ্য সফলভাবে আপডেট হয়েছে!');
                loadProfile();
                toggleEdit(false);
            } else {
                alert('আপডেট ব্যর্থ: ' + (res.db_error || 'Unknown Error'));
            }
        }).catch(() => {
            btn.innerText = "সেভ করুন";
            btn.disabled = false;
            alert('সার্ভার এরর, পুনরায় চেষ্টা করুন!');
        });
    }

    function closeProfileWindow() { window.parent.closeProfile(); }
    window.onload = loadProfile;
</script>
</body>
</html>
