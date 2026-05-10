<script>
  let users = [];
  let username = '';
  let password = '';
  let role = 'staff';

  onMount(async () => {
    await loadUsers();
  });

  async function loadUsers() {
    // Load users from API
  }

  async function addUser() {
    // Add user via API
    alert('ฟีเจอร์เพิ่มผู้ใช้');
  }
</script>

<div class="user-tab">
  <h2>จัดการผู้ใช้</h2>
  
  <div class="add-user">
    <h3>เพิ่มผู้ใช้ใหม่</h3>
    <input type="text" placeholder="ชื่อผู้ใช้" bind:value={username} />
    <input type="password" placeholder="รหัสผ่าน" bind:value={password} />
    <select bind:value={role}>
      <option value="staff">พนักงาน</option>
      <option value="admin">ผู้ดูแลระบบ</option>
      <option value="kitchen">ครัว</option>
    </select>
    <button on:click={addUser}>เพิ่มผู้ใช้</button>
  </div>

  <div class="users-list">
    <h3>รายชื่อผู้ใช้</h3>
    {#each users as user}
      <div class="user-item">
        <span>{user.username}</span>
        <span class="role">{user.role}</span>
      </div>
    {/each}
  </div>
</div>

<style>
  input, select {
    width: 100%;
    padding: 8px;
    margin: 4px 0;
  }
  button {
    padding: 8px 16px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  .user-item {
    display: flex;
    justify-content: space-between;
    padding: 8px;
    border-bottom: 1px solid #eee;
  }
  .role {
    background: #f0f0f0;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
  }
</style>