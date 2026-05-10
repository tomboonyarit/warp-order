# แผนงาน: ระบบรับ Order หน้าร้านตลาดนัด

## แนวคิดใหม่ (Mobile Web POS + KDS)

### การรับ Order
1. เริ่มรับ Order
2. เลือกสินค้า + จำนวน
3. จุดสังเกต (สีเสื้อ, ชื่อลูกค้า, ลักษณะเด่น: อ้วน/สวย/แว่น/หมวก)
4. หมายเหตุ
5. จบ Order → ไปหน้าคิว

### หน้าคิว
- เพิ่ม order ใหม่ได้
- แสดง order ที่ยังไม่เสร็จ
- กรองสถานะ: กำลังจัด / เสร็จสิ้น

### เมนู Tab
1. **สั่ง-แสดงคิว**
2. **คิวอาหารในครัว**
3. **จัดการผู้ใช้**
4. **รายงาน** (ยอดขายรายวัน)

---

## Milestone 1: Setup Project ✅
- [x] สร้าง PHP project (Composer init)
- [x] ตั้งค่า Svelte + Vite
- [x] ตั้งค่า PostgreSQL connection
- [x] กำหนด folder structure

## Milestone 2: Database Design ✅
- [x] **users** - id, username, password, role, created_at
- [x] **products** - id, name, price, category, is_active, created_at
- [x] **orders** - id, queue_number, customer_name, distinctive_notes, remark, status, user_id, created_at
- [x] **order_items** - id, order_id, product_id, quantity, price

## Milestone 3: Backend API ✅
- [x] Auth: login, logout
- [x] Products: CRUD
- [x] Orders: create, update status, list with filters
- [x] Reports: daily sales

## Milestone 4: Frontend - Tab สั่ง-แสดงคิว ✅
- [x] หน้ารับ Order
- [x] หน้าคิว

## Milestone 5: Frontend - คิวอาหารในครัว ✅
- [x] แสดง order ที่กำลังทำ
- [x] ปุ่มเปลี่ยนสถานะ

## Milestone 6: Frontend - จัดการผู้ใช้ ✅
- [x] สร้าง/แก้ไข/ลบ user
- [x] กำหนด role

## Milestone 7: Frontend - รายงาน ✅
- [x] รายงานยอดขายรายวัน

## Milestone 8: Testing & Deploy
- [ ] Test ระบบ
- [ ] Deploy