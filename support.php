<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Subscription Portal | LOHAGARA TV STUDIO</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap');
        
        body { 
            font-family: 'Outfit', sans-serif; 
            background: #0f172a; 
            color: #fff; 
            margin: 0; 
            padding: 20px; 
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
        }
        .card { 
            width: 100%; 
            max-width: 420px; 
            background: rgba(30, 41, 59, 0.7); 
            padding: 30px; 
            border-radius: 20px; 
            border: 1px solid rgba(56, 189, 248, 0.3); 
            backdrop-filter: blur(10px); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        h2 { text-align: center; color: #38bdf8; margin-bottom: 25px; font-weight: 600; }
        
        input, select { 
            width: 100%; padding: 14px; margin-bottom: 15px; border-radius: 10px; 
            background: rgba(0,0,0,0.2); border: 1px solid #334155; 
            color: white; box-sizing: border-box; outline: none; transition: 0.3s;
        }
        input:focus { border-color: #38bdf8; }
        
        .btn-wa { 
            width: 100%; padding: 15px; background: linear-gradient(135deg, #25D366, #128C7E); 
            border: none; border-radius: 10px; font-weight: bold; cursor: pointer; color: white; 
            margin-bottom: 10px; transition: transform 0.2s;
        }
        .btn-wa:active { transform: scale(0.98); }
        
        .btn-back { 
            width: 100%; padding: 14px; background: transparent; border: 1px solid #475569; 
            border-radius: 10px; color: #94a3b8; cursor: pointer; text-align: center; 
            display: block; text-decoration: none; transition: 0.3s;
        }
        .btn-back:hover { background: #334155; color: white; }
    </style>
</head>
<body>

<div class="card">
    <h2>সাবস্ক্রিপশন পোর্টাল</h2>
    <form id="supportForm">
        <input type="text" id="name" placeholder="আপনার নাম" required>
        <input type="text" id="father" placeholder="বাবার নাম" required>
        <input type="text" id="mother" placeholder="মায়ের নাম" required>
        <input type="text" id="address" placeholder="ঠিকানা" required>
        <input type="text" id="district" placeholder="জেলা" required>
        
        <select id="package" required>
            <option value="" disabled selected>প্যাকেজ নির্বাচন করুন</option>
            <?php for($i=1; $i<=12; $i++): ?>
                <option value="<?php echo $i; ?> মাস"><?php echo $i; ?> মাস</option>
            <?php endfor; ?>
        </select>

        <select id="paymentMethod" required>
            <option value="" disabled selected>পেমেন্ট মেথড</option>
            <option value="Bkash">বিকাশ (Bkash)</option>
            <option value="Nagad">নগদ (Nagad)</option>
            <option value="Rocket">রকেট (Rocket)</option>
        </select>

        <input type="number" id="senderNumber" placeholder="সেন্ডার নাম্বার" required>
        <input type="text" id="trxId" placeholder="ট্রানজেকশন আইডি (TrxID)" required>
        
        <button type="submit" class="btn-wa" id="submitBtn">তথ্য জমা দিন ও কনফার্ম করুন</button>
        <!-- ফিক্সড ব্যাক বাটন -->
        <button type="button" class="btn-back" onclick="window.location.href='index.php'">ফিরে যান (হোম পেজে)</button>
    </form>
</div>

<script>
    document.getElementById("supportForm").addEventListener("submit", function(e){
        e.preventDefault();
        const btn = document.getElementById("submitBtn");
        btn.innerText = "প্রসেস হচ্ছে...";
        btn.disabled = true;
        
        let formData = new FormData();
        formData.append('action', 'save_sub');
        formData.append('name', document.getElementById("name").value);
        formData.append('father', document.getElementById("father").value);
        formData.append('mother', document.getElementById("mother").value);
        formData.append('address', document.getElementById("address").value);
        formData.append('district', document.getElementById("district").value);
        formData.append('package', document.getElementById("package").value);
        formData.append('paymentMethod', document.getElementById("paymentMethod").value);
        formData.append('senderNumber', document.getElementById("senderNumber").value);
        formData.append('trxId', document.getElementById("trxId").value);

        fetch('support.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if(data.status === "success") {
                let msg = `🏢 *LOHAGARA TV STUDIO Subscription*\n\n` +
                          `👤 নাম: ${document.getElementById("name").value}\n` +
                          `👨‍👦 বাবা: ${document.getElementById("father").value}\n` +
                          `👩‍👦 মা: ${document.getElementById("mother").value}\n` +
                          `🏠 ঠিকানা: ${document.getElementById("address").value}, ${document.getElementById("district").value}\n` +
                          `📦 প্যাকেজ: ${document.getElementById("package").value}\n` +
                          `💰 পেমেন্ট: ${document.getElementById("paymentMethod").value}\n` +
                          `📱 সেন্ডার নাম্বার: ${document.getElementById("senderNumber").value}\n` +
                          `🆔 TrxID: ${document.getElementById("trxId").value}`;
                
                window.open(`https://wa.me/8801615896688?text=${encodeURIComponent(msg)}`, "_blank");
                alert("আপনার তথ্য সফলভাবে জমা হয়েছে!");
                document.getElementById("supportForm").reset();
            } else {
                alert("এরর: " + data.msg);
            }
            btn.innerText = "তথ্য জমা দিন ও কনফার্ম করুন";
            btn.disabled = false;
        });
    });
</script>
</body>
</html>

