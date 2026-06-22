<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Request</title>
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { width: 100%; max-width: 400px; background: #111; padding: 20px; border-radius: 10px; border: 1px solid #333; }
        h2 { text-align: center; color: #fff; margin-bottom: 20px; }
        input, select { width: 100%; padding: 12px; margin: 8px 0; background: #222; border: 1px solid #444; color: #fff; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; margin-top: 10px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px; }
        .submit-btn { background: #0056b3; color: #fff; }
        .close-btn { background: #ff0000; color: #fff; }
    </style>
</head>
<body>

<div class="container">
    <h2>সাপোর্ট রিকোয়েস্ট</h2>
    <form id="supportForm">
        <input type="text" name="name" placeholder="আপনার নাম" required>
        <input type="text" name="father" placeholder="বাবার নাম" required>
        <input type="text" name="mother" placeholder="মায়ের নাম" required>
        <input type="text" name="address" placeholder="ঠিকানা" required>
        <input type="text" name="district" placeholder="জেলা" required>
        <select name="package">
            <option value="Basic">বেসিক প্যাকেজ</option>
            <option value="Premium">প্রিমিয়াম প্যাকেজ</option>
        </select>
        <input type="text" name="paymentMethod" placeholder="পেমেন্ট মেথড (Bkash/Nagad)" required>
        <input type="text" name="senderNumber" placeholder="আপনার নাম্বার" required>
        <input type="text" name="trxId" placeholder="ট্রানজেকশন আইডি" required>
        
        <button type="submit" class="submit-btn">সাবমিট করুন</button>
    </form>
    
    <button class="close-btn" onclick="closeSupportWindow()">বন্ধ করুন</button>
</div>

<script>
    function closeSupportWindow() {
        window.parent.closeSupport();
    }

    document.getElementById('supportForm').onsubmit = function(e) {
        e.preventDefault();
        
        const name = document.querySelector('[name="name"]').value;
        const father = document.querySelector('[name="father"]').value;
        const mother = document.querySelector('[name="mother"]').value;
        const address = document.querySelector('[name="address"]').value;
        const district = document.querySelector('[name="district"]').value;
        const package = document.querySelector('[name="package"]').value;
        const method = document.querySelector('[name="paymentMethod"]').value;
        const number = document.querySelector('[name="senderNumber"]').value;
        const trxId = document.querySelector('[name="trxId"]').value;

        const msg = `সাবস্ক্রিপশন রিকোয়েস্ট:\nনাম: ${name}\nবাবা: ${father}\nমা: ${mother}\nঠিকানা: ${address}\nজেলা: ${district}\nপ্যাকেজ: ${package}\nপেমেন্ট মেথড: ${method}\nসেন্ডার নাম্বার: ${number}\nট্রানজেকশন আইডি: ${trxId}`;

        window.location.href = "https://wa.me/8801615896688?text=" + encodeURIComponent(msg);
    };
</script>

</body>
</html>