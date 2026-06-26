<template>
  <header class="navbar">
    <div class="navbar-title">
      <span class="eyebrow">Carbon tracker</span>
    </div>

    <div class="navbar-actions">
      <router-link to="/activities/log" class="btn btn-sm">
        <svg viewBox="0 0 20 20" fill="none" width="14" height="14"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        Log Activity
      </router-link>
      <button class="btn-ghost btn-sm" :disabled="resetting" @click="resetData">
        {{ resetting ? "Resetting..." : "Reset Demo" }}
      </button>
      <button class="btn-ghost btn-sm" @click="logout">Log out</button>
      <div class="navbar-avatar" :title="user?.name || 'GreenStep user'">{{ initials(user?.name) }}</div>
    </div>
  </header>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { me } from "../api/auth";
import { resetDemoData } from "../api/activity";

const router = useRouter();
const user = ref(null);
const resetting = ref(false);

function initials(name) {
  return (name || "GreenStep")
    .split(" ")
    .map((p) => p[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();
}

function logout() {
  localStorage.removeItem("token");
  router.push("/login");
}

async function resetData() {
  resetting.value = true;
  try {
    await resetDemoData();
    router.go(0);
  } finally {
    resetting.value = false;
  }
}

onMounted(async () => {
  try {
    const res = await me();
    user.value = res.data;
  } catch {
    user.value = null;
  }
});
</script>

<style scoped>
.navbar{
  height:64px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:0 32px;
  background:var(--bg-raised);
  border-bottom:1px solid var(--border);
  position:sticky;
  top:0;
  z-index:40;
}

.navbar-title .eyebrow{
  margin:0;
  background:transparent;
  padding:0;
}

.navbar-actions{
  display:flex;
  align-items:center;
  gap:10px;
}

.navbar-avatar{
  width:34px;
  height:34px;
  border-radius:50%;
  background:var(--moss-soft);
  color:var(--forest);
  display:flex;
  align-items:center;
  justify-content:center;
  font-family:var(--font-display);
  font-weight:700;
  font-size:13px;
  margin-left:4px;
}

@media (max-width:720px){
  .navbar{
    padding:0 16px;
    gap:12px;
  }

  .navbar-title{ display:none; }

  .navbar-actions{
    width:100%;
    justify-content:flex-end;
    gap:6px;
  }

  .navbar-actions .btn,
  .navbar-actions button{
    padding:8px 10px;
    font-size:12px;
  }
}
</style>
