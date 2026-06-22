<template>
  <nav class="navbar">
    <div class="navbar-spacer"></div>

    <div class="navbar-right">
      <button class="navbar-icon-btn" aria-label="Notifications">🔔</button>
      <button class="navbar-logout" @click="logout">
        Logout
        <svg viewBox="0 0 20 20" width="13" height="13" fill="none">
          <path d="M7 4H4.5A1.5 1.5 0 0 0 3 5.5v9A1.5 1.5 0 0 0 4.5 16H7M13 13l4-3-4-3M17 10H7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>
  </nav>
</template>

<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

function logout() {
  localStorage.removeItem("token");
  router.push("/login");
}
</script>

<style>
.navbar{
  position:sticky;
  top:0;
  z-index:40;
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:0 40px;
  height:64px;
  background:rgba(10,15,10,0.85);
  backdrop-filter:blur(14px);
  border-bottom:1px solid var(--border-soft);
}
.navbar-right{
  display:flex;
  align-items:center;
  gap:8px;
}
.navbar-icon-btn{
  width:34px;
  height:34px;
  border-radius:var(--r-sm);
  background:transparent;
  border:none;
  font-size:14px;
  cursor:pointer;
  display:flex;
  align-items:center;
  justify-content:center;
  transition:background var(--fast) var(--ease);
}
.navbar-icon-btn:hover{ background:var(--moss-soft); }

.navbar-logout{
  display:flex;
  align-items:center;
  gap:6px;
  background:transparent;
  border:1px solid var(--border);
  color:var(--text-muted);
  border-radius:var(--r-sm);
  padding:8px 14px;
  font-size:12.5px;
  font-weight:600;
  cursor:pointer;
  transition:all var(--fast) var(--ease);
}
.navbar-logout:hover{
  border-color:rgba(251,113,133,0.3);
  color:var(--rose);
  background:var(--rose-soft);
}
</style>
