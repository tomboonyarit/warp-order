# planning mode
    1. Task Decomposition (การแตกงานย่อย)
    Agent ห้ามกระโดดเข้าหาเป้าหมายใหญ่โดยไม่มีแผนย่อย
    Atomic Steps: ทุกแผนงานต้องถูกย่อยเป็น Task เล็กๆ ที่จบในตัวเอง (Atomic) และสามารถวัดผลได้ว่า "สำเร็จ" หรือ "ล้มเหลว"
    Dependency Mapping: หาก Task B ต้องใช้ข้อมูลจาก Task A ต้องมีการระบุลำดับความสำคัญ (Execution Order) อย่างชัดเจน
    Granularity Control: แผนงานไม่ควรมีขั้นตอนมากเกินไป (แนะนำไม่เกิน 5-7 ขั้นตอนต่อหนึ่งรอบการวางแผน) หากงานใหญ่กว่านั้น ให้แบ่งเป็น Milestone หลัก
    2. Reasoning & Justification (การให้เหตุผล)
    ก่อนเริ่มลงมือทำ Agent ต้องอธิบาย "ทำไม" ถึงเลือกแผนนั้น
    Chain of Thought (CoT): บังคับให้ Agent แสดงตรรกะเบื้องหลังการเลือกเครื่องมือ (Tools) หรือเลือกเส้นทางเดินงาน
    Alternative Consideration: ในงานที่มีความเสี่ยงสูง Agent ควรระบุแผนสำรอง (Backup Plan) หากขั้นตอนหลักล้มเหลว
    3. Dynamic Re-planning (การปรับแผนตามสถานการณ์)
    แผนงานไม่ใช่สิ่งที่แก้ไขไม่ได้
    Post-Action Evaluation: ทุกครั้งที่จบ 1 ขั้นตอน Agent ต้องประเมินผลลัพธ์ (Observation) ว่าตรงตามที่คาดไว้หรือไม่
    Pivot Logic: หากผลลัพธ์ไม่ตรงตามแผน Agent ต้องสามารถ "หยุด" และ "ร่างแผนใหม่" (Re-plan) โดยอาศัยบริบทล่าสุดได้ทันที
    Error Awareness: หากเกิด Error จากระบบหรือ API แผนงานต้องระบุวิธีการ Handle เช่น การลองใหม่ (Retry) หรือการข้ามขั้นตอนนั้นไป
    4. State & Context Management (การจัดการสถานะ)
    Agent ต้องรู้ตัวเสมอว่า "อยู่จุดไหน" ของงาน
    Progress Tracking: ต้องมีการบันทึกสถานะของแผนงานเสมอ เช่น PENDING, IN_PROGRESS, COMPLETED, FAILED
    Memory Management: ข้อมูลสำคัญที่ได้จากขั้นตอนที่ 1 ต้องถูกส่งต่อไปยังขั้นตอนที่ 2 อย่างเป็นระบบ (Context Passing)
    History Log: เก็บประวัติการตัดสินใจ (Decision Log) เพื่อใช้ในการ Debug และให้มนุษย์ตรวจสอบได้
    5. Constraint & Safety Boundary (ข้อจำกัดและความปลอดภัย)
    การวางแผนต้องอยู่ภายใต้ขอบเขตที่กำหนด
    Resource Budgeting: แผนงานต้องคำนึงถึงขีดจำกัด เช่น จำนวน Token, งบประมาณ API และเวลาในการประมวลผล (Timeout)
    Permission Check: หากแผนงานต้องเข้าถึงข้อมูลสำคัญหรือกระทำการที่ย้อนกลับไม่ได้ (Irreversible Actions) แผนนั้นต้องมีขั้นตอน "Ask for Permission" จากมนุษย์เสมอ
    Sanity Check: มีระบบตรวจสอบความสมเหตุสมผลของแผน (Plan Validator) ก่อนเริ่มรันจริง เพื่อป้องกันการทำ Loop ไม่สิ้นสุด
## change / edit mode
        🛠️ Change & Edit Mode Standards
    ในโหมดการแก้ไข (Editing) Agent ต้องยึดถือหลักการ "กระทบน้อยที่สุด แต่ได้ผลตามสั่ง" เพื่อป้องกัน Regression หรือผลกระทบข้างเคียงที่ไม่พึงประสงค์
    1. Contextual Understanding (เข้าใจก่อนแก้)
    Deep Scan: ก่อนแก้ไข Agent ต้องอ่าน Code หรือสถานะปัจจุบันในส่วนที่เกี่ยวข้องทั้งหมด (รวมถึง Dependencies) เพื่อทำความเข้าใจ Logic เดิม
    Impact Analysis: Agent ต้องประเมินและระบุว่าการแก้ไขจุดนี้ จะส่งผลกระทบต่อ Module อื่นๆ หรือไม่ก่อนเริ่มดำเนินการ
    2. Precision Editing (แก้ให้แม่นยำ)
    Targeted Changes: หลีกเลี่ยงการเขียนไฟล์ใหม่ทั้งหมด (Rewrite) ให้ใช้วิธีการแก้ไขเฉพาะจุด (Search and Replace หรือ Line-based Editing) เพื่อรักษาโครงสร้างเดิมไว้
    Minimal Invasive: ปรับเปลี่ยนเฉพาะส่วนที่จำเป็นตามคำสั่งเท่านั้น ห้ามแอบแก้ไข Style หรือ Logic อื่นที่ผู้ใช้ไม่ได้สั่ง เว้นแต่จะเป็นการแก้เพื่อให้ระบบทำงานได้จริง
    Indentation & Style Consistency: ต้องรักษา Coding Style, การย่อหน้า (Indentation) และ Naming Convention ให้ตรงกับไฟล์เดิมเสมอ
    3. Pre-Change Validation (ตรวจสอบก่อนลงมือ)
    Drafting Diffs: Agent ควรสร้าง "Diff" (การเปรียบเทียบก่อน-หลัง) ขึ้นมาในความคิดหรือใน Log เพื่อตรวจสอบความถูกต้องก่อนเขียนลงไฟล์จริง
    Constraint Checking: ตรวจสอบว่าการแก้ไขไม่ขัดต่อกฎความปลอดภัยหรือข้อกำหนดทางเทคนิคที่วางไว้ในตอนแรก
    4. Verification & Testing (หลังแก้ต้องรอด)
    Linting & Syntax Check: หลังการแก้ไขทุกครั้ง ต้องมีการรันคำสั่งเช็ค Syntax (เช่น pylint, eslint, หรือ ruff) เพื่อยืนยันว่าไม่มีจุดผิดพลาดพื้นฐาน
    Unit Test Execution: หากในระบบมี Test Suite อยู่แล้ว Agent ต้องรัน Test ในส่วนที่เกี่ยวข้องเพื่อยืนยันว่าไม่มีสิ่งที่เรียกว่า "Breaking Changes"
    Self-Correction Loop: หากการแก้ไขทำให้ Test พัง Agent ต้องเข้าสู่โหมด "Auto-Repair" เพื่อวิเคราะห์ Error และแก้ไขงานของตัวเองทันที
    5. Accountability & Rollback (โปร่งใสและถอยกลับได้)
    Change Log Annotation: ทุกการแก้ไขต้องมีการบันทึกเหตุผล (Commit Message หรือ Comment) ว่า "ทำไมถึงแก้" และ "แก้อะไรไปบ้าง"
    Version Control Integration: Agent ต้องทำงานร่วมกับ Git เสมอ โดยการสร้าง Branch ใหม่หรือการ Commit แยกตาม Task เพื่อให้มนุษย์สามารถ Rollback (ย้อนคืน) ได้หากเกิดข้อผิดพลาด

    Example of Edit Protocol (Process Flow)
    Read: อ่าน Code ปัจจุบัน
    Plan: วางแผนการแก้ (ระบุบรรทัด หรือฟังก์ชัน)
    Apply: ทำการแก้ไขเฉพาะจุด
    Verify: รัน Linter/Test
    Report: แจ้งผู้ใช้ว่าแก้จุดไหนไปบ้างพร้อมเหตุผล
### database schema changes
    เนื่องจากการแก้ไข Schema เป็นการเปลี่ยนแปลงโครงสร้างที่ย้อนกลับได้ยากและกระทบต่อข้อมูล Agent ต้องปฏิบัติตามกฎเหล็กดังนี้:
    1. Pre-Change Analysis & Mapping
    Schema Discovery: Agent ต้องดึงโครงสร้างปัจจุบัน (Table, Column, Index, Constraints) และ ER Diagram (ถ้ามี) มาวิเคราะห์ความสัมพันธ์ (Relationships) ทั้งหมดก่อนเริ่มวางแผน
    Data Impact Assessment: วิเคราะห์ว่าการแก้ไขจะส่งผลต่อข้อมูลเดิมอย่างไร (เช่น การเปลี่ยน Data Type อาจทำให้ข้อมูลเดิมสูญหาย หรือการเพิ่ม NOT NULL โดยไม่มี DEFAULT จะทำให้ Insert ข้อมูลไม่ได้)
    2. Migration-First Approach
    No Direct SQL Execution: ห้ามรันคำสั่ง ALTER TABLE หรือ DROP บนฐานข้อมูลโดยตรง Agent ต้องสร้างไฟล์ Migration Script (เช่น Python Alembic, Node.js Sequelize Migrations, หรือ SQL Migrations) เสมอ
    Idempotency: Script ที่สร้างขึ้นต้องเป็น Idempotent (รันซ้ำกี่ครั้งก็ให้ผลลัพธ์คงเดิม) เพื่อป้องกัน Error เมื่อระบบรัน Migration ซ้ำ
    Forward & Backward Compatibility: ทุกการแก้ไขโครงสร้าง (Up) ต้องมีคำสั่งสำหรับย้อนกลับ (Down/Rollback) เสมอ
    3. Backward Compatibility (Zero Downtime Principles)
    เพื่อป้องกันไม่ให้ Application พังขณะอัปเดต Agent ต้องยึดหลัก "Expand and Contract":
    Add Before Delete: หากต้องการเปลี่ยนชื่อ Column ให้สร้าง Column ใหม่ -> ย้ายข้อมูล -> แล้วค่อยลบ Column เก่าในภายหลัง (แยกเป็นคนละขั้นตอน)
    Nullable by Default: เมื่อเพิ่ม Column ใหม่ใน Table ที่มีข้อมูลอยู่แล้ว ควรเริ่มด้วยการอนุญาตให้เป็น NULL หรือมี DEFAULT Value เพื่อไม่ให้ Query เดิมพัง
    4. Data Safety & Integrity
    Destructive Action Warnings: หากแผนงานมีการใช้คำสั่ง DROP TABLE, DROP COLUMN หรือ TRUNCATE Agent ต้องทำการ "Double-Check" และแจ้งเตือนผู้ใช้อย่างชัดเจนถึงความเสี่ยงของการสูญเสียข้อมูล
    Backup Requirement: ก่อนรันคำสั่งที่กระทบต่อโครงสร้าง Agent ควรอ้างอิงถึงขั้นตอนการทำ Backup หรือ Snapshot ของฐานข้อมูลก่อนเสมอ
    5. Validation & Testing
    Dry Run: หาก Tool รองรับ Agent ต้องทำการรันในโหมด Dry Run เพื่อดูคำสั่ง SQL ที่จะเกิดขึ้นจริงก่อนกดยืนยัน
    Relationship Integrity: หลังแก้ Schema ต้องตรวจสอบว่า Foreign Keys และ Indexes ยังคงทำงานถูกต้องและไม่เกิดคอขวด (Performance Bottleneck)

    Agent should never perform Schema changes on a production database without an explicit 'Approval' from a human administrator.

    Example of Database Change Protocol
    ขั้นตอนการดำเนินการ
    1. Inspectอ่าน Schema ปัจจุบันและเช็คจำนวนแถวข้อมูลเบื้องต้น
    2. Designร่างไฟล์ Migration (เช่น 20231027_add_user_bio.sql)
    3. Risk Checkตรวจสอบว่ามีคำสั่งลบข้อมูลหรือไม่
    4. Testรัน Migration ใน Database จำลอง (Staging/Dev)
    5. Deployรัน Migration ใน Prod พร้อมบันทึก Log และแผน Rollback

#### Golden Rule of Editing
    "Never delete code you don't fully understand, and never fix what isn't broken unless explicitly instructed."
    (ห้ามลบ Code ที่ไม่เข้าใจ และห้ามแก้ส่วนที่ไม่ได้พังเว้นแต่จะมีคำสั่งชัดเจน)
