<template>
  <aside class="sidebar">
    <div class="sidebar-brand">
      <span class="brand-mark">GS</span>
      <span class="brand-name">GreenStep</span>
    </div>

    <nav class="sidebar-nav">
      <p class="nav-label">Track</p>
      <router-link to="/" class="nav-item" exact-active-class="is-active">
        <svg viewBox="0 0 20 20" fill="none"><path d="M3 10.5 10 4l7 6.5M5 9v7h10V9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Dashboard</span>
      </router-link>
      <router-link to="/activities/log" class="nav-item" active-class="is-active">
        <svg viewBox="0 0 20 20" fill="none"><path d="M11 2 4 11.5h4.5L9 18l7-9.5h-4.5L11 2Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
        <span>Log Activity</span>
      </router-link>
      <router-link to="/activities" class="nav-item" active-class="is-active">
        <svg viewBox="0 0 20 20" fill="none"><path d="M4 5h12M4 10h12M4 15h7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        <span>Today</span>
      </router-link>
      <router-link to="/goals" class="nav-item" active-class="is-active">
        <svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.6"/><circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.6"/><circle cx="10" cy="10" r="0.6" fill="currentColor"/></svg>
        <span>My Goal</span>
      </router-link>

      <p class="nav-label">Community</p>
      <router-link to="/challenges" class="nav-item" active-class="is-active">
        <svg viewBox="0 0 20 20" fill="none"><path d="M5 2v16M5 3h9l-2.5 3L14 9H5" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
        <span>Challenges</span>
      </router-link>
      <router-link to="/friends" class="nav-item" active-class="is-active">
        <svg viewBox="0 0 20 20" fill="none"><circle cx="7" cy="6.5" r="2.5" stroke="currentColor" stroke-width="1.6"/><circle cx="14" cy="7.5" r="2" stroke="currentColor" stroke-width="1.6"/><path d="M2.5 16c0-2.5 2-4.2 4.5-4.2s4.5 1.7 4.5 4.2M12.5 12.5c2 .1 3.5 1.5 3.5 3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        <span>Friends</span>
      </router-link>
      <router-link to="/friends/requests" class="nav-item" active-class="is-active">
        <svg viewBox="0 0 20 20" fill="none"><circle cx="7" cy="7" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M2 16c0-2.8 2.2-4.6 5-4.6s5 1.8 5 4.6M14 6v4M16 8h-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        <span>Requests</span>
      </router-link>
      <router-link to="/leaderboard" class="nav-item" active-class="is-active">
        <svg viewBox="0 0 20 20" fill="none"><path d="M4 17V9M10 17V3M16 17v-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        <span>Leaderboard</span>
      </router-link>
      <router-link to="/photos" class="nav-item" active-class="is-active">
        <svg viewBox="0 0 20 20" fill="none"><rect x="2.5" y="4" width="15" height="12" rx="2" stroke="currentColor" stroke-width="1.6"/><circle cx="7" cy="9" r="1.3" stroke="currentColor" stroke-width="1.4"/><path d="M3 14.5 7.5 11l3 2.5 2.5-3 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Eco Photos</span>
      </router-link>
    </nav>

    <div class="sidebar-footer">
      <span class="eyebrow">Every log counts</span>
    </div>
  </aside>
</template>

<style scoped>
.sidebar{
  width:232px;
  flex-shrink:0;
  background:var(--forest);
  color:var(--text-on-forest);
  display:flex;
  flex-direction:column;
  min-height:calc(100vh - 64px);
  padding:22px 16px 24px;
}

.sidebar-brand{
  display:flex;
  align-items:center;
  gap:10px;
  padding:4px 10px 26px;
  font-family:var(--font-display);
  font-size:19px;
  font-weight:600;
  letter-spacing:-0.01em;
}

.brand-mark{
  width:28px;
  height:28px;
  border-radius:50%;
  background:rgba(238,243,234,0.12);
  border:1px solid rgba(238,243,234,0.18);
  display:flex;
  align-items:center;
  justify-content:center;
  font-family:var(--font-mono);
  font-size:10px;
}

.sidebar-nav{
  flex:1;
  display:flex;
  flex-direction:column;
  gap:2px;
}

.nav-label{
  font-family:var(--font-mono);
  font-size:10.5px;
  letter-spacing:0.1em;
  text-transform:uppercase;
  color:rgba(238,243,234,0.45);
  margin:18px 10px 8px;
}

.nav-label:first-child{ margin-top:0; }

.nav-item{
  display:flex;
  align-items:center;
  gap:11px;
  padding:9px 10px;
  border-radius:var(--r-sm);
  font-size:13.5px;
  font-weight:500;
  color:rgba(238,243,234,0.78);
  transition:background var(--fast) var(--ease), color var(--fast) var(--ease);
}

.nav-item svg{
  width:17px;
  height:17px;
  flex-shrink:0;
}

.nav-item:hover{
  background:rgba(238,243,234,0.08);
  color:var(--text-on-forest);
}

.nav-item.is-active{
  background:rgba(238,243,234,0.13);
  color:#fff;
  font-weight:600;
}

.sidebar-footer{
  padding:14px 10px 2px;
  border-top:1px solid rgba(238,243,234,0.12);
  margin-top:14px;
}

.sidebar-footer .eyebrow{
  color:var(--moss);
  background:transparent;
  padding:0;
  margin:0;
}

@media (max-width:720px){
  .sidebar{ display:none; }
}
</style>
