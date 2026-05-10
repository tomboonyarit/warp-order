<script>
  let users = $state([
    { username: "admin", role: "admin", status: "พร้อมใช้งาน" }
  ]);
  let username = $state("");
  let password = $state("");
  let role = $state("staff");

  async function addUser() {
    alert("ฟีเจอร์เพิ่มผู้ใช้ยังไม่ได้ต่อ API");
  }
</script>

<section class="user-board panel">
  <div class="section-head">
    <div>
      <p class="eyebrow">Access Control</p>
      <h2 class="section-title">จัดการผู้ใช้</h2>
      <p class="section-subtitle">เตรียมพื้นที่สำหรับเพิ่มพนักงานหน้าร้าน ครัว และผู้ดูแลระบบ</p>
    </div>
  </div>

  <div class="user-layout">
    <form class="user-form" onsubmit={(event) => { event.preventDefault(); addUser(); }}>
      <h3>เพิ่มผู้ใช้ใหม่</h3>
      <label>
        ชื่อผู้ใช้
        <input type="text" placeholder="staff01" bind:value={username} />
      </label>
      <label>
        รหัสผ่าน
        <input type="password" placeholder="อย่างน้อย 8 ตัวอักษร" bind:value={password} />
      </label>
      <label>
        สิทธิ์การใช้งาน
        <select bind:value={role}>
          <option value="staff">พนักงานหน้าร้าน</option>
          <option value="admin">ผู้ดูแลระบบ</option>
          <option value="kitchen">ครัว</option>
        </select>
      </label>
      <button class="primary-action" type="submit">เพิ่มผู้ใช้</button>
    </form>

    <div class="users-list">
      <div class="list-head">
        <h3>ทีมปัจจุบัน</h3>
        <span>{users.length} คน</span>
      </div>
      {#each users as user (user.username)}
        <div class="user-item">
          <div class="avatar">{user.username.slice(0, 1).toUpperCase()}</div>
          <div>
            <strong>{user.username}</strong>
            <span>{user.status}</span>
          </div>
          <span class="role">{user.role}</span>
        </div>
      {/each}
    </div>
  </div>
</section>

<style>
  .user-board {
    padding: 24px;
  }

  .section-head {
    margin-bottom: 18px;
  }

  .user-layout {
    display: grid;
    grid-template-columns: 420px minmax(0, 1fr);
    gap: 16px;
    align-items: start;
  }

  .user-form,
  .users-list {
    border: 1px solid var(--line);
    border-radius: 24px;
    padding: 18px;
    background: rgba(255, 255, 255, 0.58);
  }

  h3 {
    margin: 0 0 14px;
    font-size: 22px;
    letter-spacing: -0.03em;
  }

  .user-form {
    display: grid;
    gap: 12px;
  }

  label {
    display: grid;
    gap: 6px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  input,
  select {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 12px 14px;
    color: var(--ink);
    background: rgba(255, 255, 255, 0.78);
  }

  .list-head,
  .user-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  .list-head span {
    border-radius: 999px;
    padding: 7px 10px;
    color: var(--blue);
    font-size: 12px;
    font-weight: 900;
    background: var(--blue-soft);
  }

  .user-item {
    justify-content: flex-start;
    border-top: 1px solid var(--line);
    padding: 14px 0;
  }

  .avatar {
    display: grid;
    width: 44px;
    height: 44px;
    place-items: center;
    border-radius: 16px;
    color: var(--brand-dark);
    font-weight: 950;
    background: #fff1d2;
  }

  .user-item div:nth-child(2) {
    min-width: 0;
    flex: 1;
  }

  .user-item strong,
  .user-item span {
    display: block;
  }

  .user-item span {
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
  }

  .role {
    border-radius: 999px;
    padding: 7px 10px;
    color: var(--green) !important;
    background: var(--green-soft);
  }

  @media (max-width: 900px) {
    .user-layout {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 640px) {
    .user-board {
      padding: 16px;
      border-radius: 22px;
    }
  }
</style>
