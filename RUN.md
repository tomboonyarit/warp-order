# วิธีการรันโปรเจค Warp Order

## โครงสร้างโปรเจค

โปรเจคนี้ประกอบด้วย 2 ส่วนหลัก:
1. **Frontend** - Svelte + Vite (พอร์ต 5173)
2. **Backend** - PHP API (พอร์ต 8080)

---

## ขั้นตอนการรันที่ถูกต้อง

### 1. ตั้งค่า Database (ครั้งแรกเท่านั้น)

```bash
cd D:\DDrive\21_opencode\warp-order
php setup-db.php
```

**ผลลัพธ์ที่คาดหวัง:**
```
Database created successfully!
```

สร้างไฟล์ SQLite database และข้อมูลเริ่มต้น (users, products, menu_categories, note_groups)

---

### 2. รัน Frontend (Svelte Dev Server)

```bash
cd D:\DDrive\21_opencode\warp-order\resources\svelte-app
node ./node_modules/vite/bin/vite.js --port 5173
```

**หรือ** (ถ้าใช้ PowerShell ตรง ๆ):
```powershell
cd D:\DDrive\21_opencode\warp-order\resources\svelte-app
Start-Job -ScriptBlock { node ./node_modules/vite/bin/vite.js } | Receive-Job -Wait -AutoRemove
```

**หรือ** รันแบบ hidden process:
```powershell
Start-Process -FilePath "node" -ArgumentList "./node_modules/vite/bin/vite.js" -WorkingDirectory "D:\DDrive\21_opencode\warp-order\resources\svelte-app" -WindowStyle Hidden
```

**ผลลัพธ์ที่คาดหวัง:**
```
VITE v8.0.11 ready in xxx ms
➜  Local:   http://localhost:5173/
➜  Network: use --host to expose
```

---

### 3. รัน Backend (PHP Server)

```bash
cd D:\DDrive\21_opencode\warp-order
php -S 127.0.0.1:8080 -t public
```

**หรือ** รันแบบ hidden process:
```powershell
Start-Process -FilePath "php" -ArgumentList "-S","127.0.0.1:8080","-t","public" -WorkingDirectory "D:\DDrive\21_opencode\warp-order" -WindowStyle Hidden
```

**ผลลัพธ์ที่คาดหวัง:**
```
[Thu May 14 xx:xx:xx 2026] PHP 7.4.33 Development Server (http://127.0.0.1:8080) started
```

---

## การเข้าถึง

- **เว็บไซต์ (Frontend)**: http://localhost:5173
- **API Backend**: http://localhost:8080/api

---

## ข้อมูลล็อกอินเริ่มต้น

- Username: `admin`
- Password: `admin`

---

## ปัญหาที่พบและวิธีแก้ไข

### ปัญหา 1: npm หา package.json ไม่เจอ

**อาการ:** npm error ENOENT เมื่อรัน `npm run dev`

**สาเหตุ:** รันคำสั่งในโฟลเดอร์หลักแทนที่จะอยู่ใน `resources/svelte-app`

**แก้ไข:**
```bash
cd resources/svelte-app
npm install
npm run dev
```
หรือใช้ `node ./node_modules/vite/bin/vite.js` แทน `npm run dev`

---

### ปัญหา 2: PowerShell ไม่รองรับ `&&` operator

**อาการ:** ParserError เมื่อรันคำสั่งที่มี `&&`

**แก้ไข:** ใช้ semicolon (`;`) แทน หรือรันคำสั่งแยกกัน

```powershell
cd resources/svelte-app; npm install
```
หรือ
```powershell
cd resources/svelte-app
npm install
```

---

### ปัญหา 3: Vite หรือ PHP server หยุดทำงานทันทีหลังรัน

**อาการ:** Server เริ่มทำงานแต่หยุดทันทีเมื่อไม่มี interactive input

**แก้ไข:**
- ใช้ `Start-Job` สำหรับ PowerShell
- ใช้ `-WindowStyle Hidden` กับ `Start-Process`
- รันใน separate terminal

---

### ปัญหา 4: เช็คสถานะ Server

**วิธีเช็คว่า Server รันอยู่หรือไม่:**

```powershell
Get-NetTCPConnection -LocalPort 5173 -ErrorAction SilentlyContinue | Select-Object LocalPort, State
Get-NetTCPConnection -LocalPort 8080 -ErrorAction SilentlyContinue | Select-Object LocalPort, State
```

**ผลลัพธ์ที่ถูกต้อง:**
```
LocalPort  State
---------  -----
     5173 Listen
     8080 Listen
```

---

### ปัญหา 5: curl command ใน PowerShell

**อาการ:** PowerShell ใช้ alias `curl` ที่เป็น `Invoke-WebRequest` ซึ่งต้องการ `-Uri` parameter

**แก้ไข:** ใช้ `Invoke-WebRequest` โดยตรง

```powershell
Invoke-WebRequest -Uri http://localhost:5173 -UseBasicParsing | Select-Object -ExpandProperty Content
```

---

## การทดสอบว่ารันได้สำเร็จ

### เทส Frontend
```powershell
Invoke-WebRequest -Uri http://localhost:5173 -UseBasicParsing | Select-Object -ExpandProperty Content
```
**ผลลัพธ์ที่ถูกต้อง:** HTML page ที่มี `<title>Warp Order</title>`

### เทส Backend API
```powershell
Invoke-WebRequest -Uri http://localhost:8080/api/products -UseBasicParsing | Select-Object -ExpandProperty Content
```
**ผลลัพธ์ที่ถูกต้อง:** JSON array ของสินค้า

---

## Scripts ที่มีใน package.json

```bash
npm run dev      # รัน development server
npm run build    # build สำหรับ production
npm run preview # ดู production build
```

---

## ข้อมูลเพิ่มเติม

- **vite.config.js** proxy `/api` requests ไปที่ `http://127.0.0.1:8080`
- **database** ใช้ SQLite เก็บที่ `database/app.db`
- **PHP version** ที่รองรับ: 7.4+