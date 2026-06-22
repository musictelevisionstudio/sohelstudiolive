<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <style>
        body { background: #1a1a1a; color: #fff; font-family: sans-serif; padding: 20px; }
        .container { max-width: 400px; margin: auto; }
        input, select { width: 100%; padding: 10px; margin: 5px 0; border-radius: 5px; border: none; }
        button { width: 100%; padding: 10px; background: #1565d8; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>সাপোর্ট সেন্টার</h2>
        <form id="supportForm">
            <input type="text" name="name" placeholder="নাম" required>
            <input type="text" name="father" placeholder="বাবার নাম" required>
            <input type="text" name="mother" placeholder="মায়ের নাম" required>
            <input type="text" name="address" placeholder="ঠিকানা" required>
            <input type="text" name="district" placeholder="জেলা" required>
            <select name="package">
                <option value="basic">বেসিক প্যাকেজ</option>
                <option value="premium">প্রিমিয়াম প্যাকেজ</option>
            </select>
            <input type="text" name="paymentMethod" placeholder="পেমেন্ট মেথড (Bkash/Nagad)" required>
            <input type="text" name="senderNumber" placeholder="আপনার নাম্বার" required>
            <input type="text" name="trxId" placeholder="ট্রানজেকশন আইডি" required>
            <button type="submit">সাবমিট করুন</button>
        </form>
        <p id="responseMsg" style="text-align:center; margin-top:10px;"></p>
    </div>

    <script>
        document.getElementById('supportForm').onsubmit = async function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let res = await fetch('support.php', { method: 'POST', body: formData });
            let data = await res.json();
            document.getElementById('responseMsg').innerText = data.msg;
            if(data.status === 'success') {
                setTimeout(() => window.parent.closeSupport(), 2000);
            }
        };
    </script>
</body>
</html>
