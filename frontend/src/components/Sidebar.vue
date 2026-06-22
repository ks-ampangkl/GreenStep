<template>
  <aside class="sidebar">
    <div class="sidebar-brand">
      <span class="sidebar-logo">🌿</span>
      <span class="sidebar-title">GreenStep</span>
    </div>

    <nav class="sidebar-nav">
      <span class="sidebar-section">Main</span>
      <router-link to="/" exact-active-class="active"><span class="ic">📊</span><span class="label">Dashboard</span></router-link>
      <router-link to="/activities" exact-active-class="active"><span class="ic">📝</span><span class="label">Activities</span></router-link>
      <router-link to="/activities/log" exact-active-class="active"><span class="ic">➕</span><span class="label">Log Activity</span></router-link>
      <router-link to="/goals" exact-active-class="active"><span class="ic">🎯</span><span class="label">Goals</span></router-link>

      <span class="sidebar-section">Community</span>
      <router-link to="/challenges" exact-active-class="active"><span class="ic">🏆</span><span class="label">Challenges</span></router-link>
      <router-link to="/friends" exact-active-class="active"><span class="ic">👥</span><span class="label">Friends</span></router-link>
      <router-link to="/friends/requests" exact-active-class="active"><span class="ic">📬</span><span class="label">Requests</span></router-link>
      <router-link to="/leaderboard" exact-active-class="active"><span class="ic">🏅</span><span class="label">Leaderboard</span></router-link>
      <router-link to="/photos" exact-active-class="active"><span class="ic">📸</span><span class="label">Photos</span></router-link>
    </nav>
  </aside>
</template>

<style>
.sidebar{
  width:230px;
  flex-shrink:0;
  height:100vh;
  position:sticky;
  top:0;
  background:var(--bg-raised);
  border-right:1px solid var(--border-soft);
  display:flex;
  flex-direction:column;
  padding:22px 16px;
}

.sidebar-brand{
  display:flex;
  align-items:center;
  gap:9px;
  padding:0 8px;
  margin-bottom:28px;
}
.sidebar-logo{ font-size:19px; }
.sidebar-title{
  font-size:15px;
  font-weight:800;
  color:#fff;
  letter-spacing:-0.01em;
}

.sidebar-nav{
  display:flex;
  flex-direction:column;
  gap:2px;
}

.sidebar-section{
  font-size:10px;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:0.08em;
  color:var(--text-faint);
  padding:14px 10px 8px;
}
.sidebar-section:first-child{ padding-top:0; }

.sidebar-nav a{
  display:flex;
  align-items:center;
  gap:11px;
  padding:9px 10px;
  border-radius:var(--r-sm);
  color:var(--text-muted);
  font-size:13px;
  font-weight:600;
  text-decoration:none;
  transition:background var(--fast) var(--ease), color var(--fast) var(--ease);
}
.sidebar-nav a .ic{
  font-size:14px;
  width:18px;
  text-align:center;
  flex-shrink:0;
}
.sidebar-nav a:hover{
  background:var(--bg-hover);
  color:var(--text);
}
.sidebar-nav a.active{
  background:var(--moss-soft);
  color:var(--forest-bright);
}

@media (max-width:700px){
  .sidebar{ width:64px; padding:18px 8px; }
  .sidebar-title, .sidebar-section{ display:none; }
  .sidebar-brand{ justify-content:center; padding:0; }
  .sidebar-nav a{ justify-content:center; padding:11px 0; }
  .sidebar-nav a span:not(.ic){ display:none; }
}
</style>
