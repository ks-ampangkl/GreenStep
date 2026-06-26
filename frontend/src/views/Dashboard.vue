<template>
  <div class="page-head">
    <div>
      <span class="eyebrow">{{ greeting }}</span>
      <h1>Your carbon dashboard</h1>
      <p class="page-subtitle">Here's where things stand today.</p>
    </div>
    <router-link to="/activities/log" class="btn">
      <svg viewBox="0 0 20 20" width="14" height="14" fill="none"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      Log Activity
    </router-link>
  </div>

  <!-- stat cards -->
  <div class="stats-grid">
    <div class="card stat-card">
      <div class="stat-head">
        <span class="stat-label">Logged today</span>
        <span class="stat-icon green">📝</span>
      </div>
      <div class="stat-value">{{ todayActivities.length }}</div>
      <p class="stat-foot">activities recorded</p>
    </div>

    <div class="card stat-card">
      <div class="stat-head">
        <span class="stat-label">Today's footprint</span>
        <span class="stat-icon clay">🌫️</span>
      </div>
      <div class="stat-value">{{ todayEmissions.toFixed(1) }}<span class="stat-unit">kg</span></div>
      <p class="stat-foot">CO₂e across {{ todayActivities.length }} logs</p>
    </div>

    <div class="card stat-card">
      <div class="stat-head">
        <span class="stat-label">Goal progress</span>
        <span class="stat-icon amber">🎯</span>
      </div>
      <div class="stat-value">{{ goal ? goal.progress.progress_percent : '–' }}<span v-if="goal" class="stat-unit">%</span></div>
      <p class="stat-foot">{{ goal ? goal.current_goal.title : 'No goal set yet' }}</p>
    </div>

    <div class="card stat-card">
      <div class="stat-head">
        <span class="stat-label">Open challenges</span>
        <span class="stat-icon teal">🏆</span>
      </div>
      <div class="stat-value">{{ challenges.length }}</div>
      <p class="stat-foot">you can join right now</p>
    </div>
  </div>

  <!-- mid row: today's log + weekly chart -->
  <div class="mid-grid">
    <div class="card">
      <div class="card-header">
        <h2>Today's log</h2>
        <span class="tag">{{ todayActivities.length }} logged</span>
      </div>

      <div v-if="todayActivities.length" class="act-list">
        <div v-for="a in todayActivities.slice(0, 5)" :key="a.id" class="act-item">
          <div class="act-info">
            <p class="act-name">{{ a.activity }}</p>
            <span class="tag">{{ a.amount }}</span>
          </div>
          <div class="act-co2 mono">{{ a.emissions_kg }} kg</div>
        </div>
      </div>
      <div v-else class="empty-state empty-state--inline">
        <span class="empty-icon">📭</span>
        Nothing logged yet today.
      </div>

      <router-link to="/activities/log" class="btn-outline-block">+ Log an activity</router-link>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>Footprint this week</h2>
        <span class="chart-legend"><span class="legend-dot"></span> kg CO₂e</span>
      </div>

      <div v-if="weeklyData.length" class="chart-bars">
        <div v-for="(day, i) in weeklyData" :key="i" class="bar-col">
          <span class="bar-val">{{ day.value.toFixed(1) }}</span>
          <div class="bar" :class="day.isToday ? 'bar-today' : 'bar-past'" :style="{ height: barHeight(day.value) }"></div>
          <span class="bar-day" :class="day.isToday ? 'bar-day-today' : ''">{{ day.label }}</span>
        </div>
      </div>
      <div v-else class="empty-state empty-state--inline">
        <span class="empty-icon">📊</span>
        No history yet this week.
      </div>

      <div class="chart-footer">
        <span class="muted-text">Weekly total</span>
        <span class="white-text">{{ weeklyTotal }} kg CO₂e</span>
      </div>
    </div>
  </div>

  <!-- bottom row: goal trail + challenges -->
  <div class="bottom-grid">
    <div class="card">
      <div class="card-header">
        <h2>Goal trail</h2>
        <router-link to="/goals" class="link-btn">Edit →</router-link>
      </div>

      <template v-if="goal">
        <p class="trail-title">{{ goal.current_goal.title }}</p>
        <div class="trail-progress">
          <div class="trail-progress-fill" :style="{ width: goal.progress.progress_percent + '%' }"></div>
          <div class="trail-progress-marker" :style="{ left: goal.progress.progress_percent + '%' }">🏃</div>
        </div>
        <div class="trail-meta">
          <span>Start</span>
          <span class="mono">{{ goal.progress.progress_percent }}%</span>
          <span>Target</span>
        </div>
      </template>
      <div v-else class="empty-state empty-state--inline">
        <span class="empty-icon">🎯</span>
        No goal set yet.
        <router-link to="/goals" class="link-btn">Set one →</router-link>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>Challenges</h2>
        <router-link to="/challenges" class="link-btn">Browse →</router-link>
      </div>

      <div v-if="challenges.length" class="ch-list">
        <div v-for="c in challenges.slice(0, 3)" :key="c.id" class="ch-item">
          <div class="ch-icon">🏆</div>
          <div class="act-info">
            <p class="act-name">{{ c.title }}</p>
            <span class="tag">{{ c.target_type }}</span>
          </div>
        </div>
      </div>
      <div v-else class="empty-state empty-state--inline">
        <span class="empty-icon">🏆</span>
        No active challenges.
      </div>
    </div>
  </div>

  <!-- leaderboard preview -->
  <div class="card">
    <div class="card-header">
      <h2>Leaderboard</h2>
      <router-link to="/leaderboard" class="link-btn">View all →</router-link>
    </div>

    <table v-if="leaderboard.length">
      <tr>
        <th>Rank</th>
        <th>Name</th>
        <th>Points</th>
      </tr>
      <tr v-for="(u, i) in leaderboard.slice(0, 5)" :key="u.id">
        <td class="rank-cell">{{ medal(i) }}</td>
        <td>
          <div class="lb-user">
            <div class="avatar avatar-sm">{{ initials(u.name) }}</div>
            {{ u.name }}
          </div>
        </td>
        <td class="mono points-cell">{{ u.eco_points }}</td>
      </tr>
    </table>
    <div v-else class="empty-state empty-state--inline">
      <span class="empty-icon">🏅</span>
      The leaderboard is empty for now.
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { getDashboard, getTodayLogs, getHistory, getLeaderboard } from "../api/activity";
import { getGoal } from "../api/goal";
import { getChallenges } from "../api/challenge";

const todayActivities = ref([]);
const history = ref([]);
const goal = ref(null);
const challenges = ref([]);
const leaderboard = ref([]);
const dashboard = ref(null);

const greeting = computed(() => {
  const h = new Date().getHours();
  if (h < 12) return "Good morning";
  if (h < 17) return "Good afternoon";
  return "Good evening";
});

const todayEmissions = computed(() =>
  todayActivities.value.reduce((sum, a) => sum + (parseFloat(a.emissions_kg) || 0), 0)
);

const weeklyData = computed(() => {
  const days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
  const today = new Date().getDay();
  const todayIdx = today === 0 ? 6 : today - 1;
  const dayIndex = {
    monday: 0,
    tuesday: 1,
    wednesday: 2,
    thursday: 3,
    friday: 4,
    saturday: 5,
    sunday: 6,
  };

  const totals = new Array(7).fill(0);
  history.value.forEach((item) => {
    const rawDate = item?.date || item?.day;
    if (!rawDate) return;

    let idx = dayIndex[String(rawDate).toLowerCase()];
    if (idx === undefined) {
      const d = new Date(rawDate);
      if (Number.isNaN(d.getTime())) return;
      const dow = d.getDay();
      idx = dow === 0 ? 6 : dow - 1;
    }

    totals[idx] += parseFloat(item.total_emissions_kg ?? item.emissions_kg) || 0;
  });

  return days.map((label, i) => ({ label, value: totals[i], isToday: i === todayIdx }));
});

const weeklyTotal = computed(() =>
  weeklyData.value.reduce((s, d) => s + d.value, 0).toFixed(1)
);

function barHeight(value) {
  const max = Math.max(...weeklyData.value.map((d) => d.value), 1);
  return `${Math.max(Math.round((value / max) * 110), 3)}px`;
}

function initials(name) {
  return (name || "").split(" ").map((p) => p[0]).join("").slice(0, 2).toUpperCase();
}

function medal(i) {
  return i === 0 ? "🥇" : i === 1 ? "🥈" : i === 2 ? "🥉" : `#${i + 1}`;
}

onMounted(async () => {
  const results = await Promise.allSettled([
    getDashboard(),
    getTodayLogs(),
    getHistory(),
    getGoal(),
    getChallenges(),
    getLeaderboard(),
  ]);

  if (results[0].status === "fulfilled") dashboard.value = results[0].value.data ?? null;
  if (results[1].status === "fulfilled") todayActivities.value = results[1].value.data ?? [];
  if (results[2].status === "fulfilled") history.value = results[2].value.data ?? [];
  if (results[3].status === "fulfilled") {
    const data = results[3].value.data;
    goal.value = data?.current_goal ? data : null;
  }
  if (results[4].status === "fulfilled") challenges.value = results[4].value.data ?? [];
  if (results[5].status === "fulfilled") leaderboard.value = results[5].value.data?.leaderboard ?? [];

  if (dashboard.value?.charts?.weekly_history_graph?.length && !history.value.length) {
    history.value = dashboard.value.charts.weekly_history_graph.map((row) => ({
      day: row.day,
      total_emissions_kg: row.emissions_kg,
    }));
  }
});
</script>

<style scoped>
.stats-grid{
  display:grid;
  grid-template-columns:repeat(4, 1fr);
  gap:14px;
  margin-bottom:14px;
}
.stat-card{ margin-bottom:0; cursor:default; }
.stat-head{
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:14px;
}
.stat-label{
  font-size:10.5px;
  text-transform:uppercase;
  letter-spacing:0.07em;
  color:var(--text-faint);
  font-weight:700;
}
.stat-icon{
  width:32px;
  height:32px;
  border-radius:var(--r-sm);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:14px;
  transition:filter var(--fast) var(--ease);
}
.stat-card:hover .stat-icon{ filter:brightness(1.2); }
.stat-icon.green{ background:var(--moss-soft); }
.stat-icon.clay{ background:var(--clay-soft); }
.stat-icon.amber{ background:rgba(251,191,36,0.12); }
.stat-icon.teal{ background:rgba(45,212,191,0.12); }

.stat-value{
  font-size:27px;
  font-weight:800;
  color:var(--forest);
  line-height:1;
  letter-spacing:-0.02em;
}
.stat-unit{ font-size:13px; font-weight:500; color:var(--text-faint); margin-left:3px; }
.stat-foot{ font-size:11.5px; margin-top:7px; color:var(--text-faint); }

.card-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:16px;
}

.mid-grid{
  display:grid;
  grid-template-columns:1fr 1.4fr;
  gap:14px;
  margin-bottom:14px;
  align-items:start;
}
.bottom-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:14px;
  margin-bottom:14px;
  align-items:start;
}

.act-list{ display:flex; flex-direction:column; gap:8px; margin-bottom:14px; }
.act-item{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:10px;
  padding:10px 12px;
  border-radius:var(--r-sm);
  background:var(--bg-input);
  border:1px solid var(--border-soft);
  transition:all var(--fast) var(--ease);
}
.act-item:hover{ border-color:var(--border-hover); background:var(--moss-soft); }
.act-info{ display:flex; align-items:center; gap:10px; min-width:0; }
.act-name{ font-size:12.5px; font-weight:600; color:var(--text); }
.act-co2{ font-size:12.5px; font-weight:700; color:var(--clay); white-space:nowrap; }

.btn-outline-block{
  display:block;
  text-align:center;
  width:100%;
  background:transparent;
  border:1px solid rgba(52,211,153,0.25);
  color:var(--forest);
  border-radius:var(--r-sm);
  padding:10px;
  font-size:12.5px;
  font-weight:600;
  transition:background var(--fast) var(--ease);
}
.btn-outline-block:hover{ background:var(--moss-soft); }

.chart-legend{ display:flex; align-items:center; gap:6px; font-size:11px; color:var(--text-faint); }
.legend-dot{ width:8px; height:8px; border-radius:50%; background:var(--forest); display:inline-block; }
.chart-bars{ display:flex; align-items:flex-end; gap:10px; height:130px; margin-bottom:10px; }
.bar-col{ flex:1; display:flex; flex-direction:column; align-items:center; gap:5px; }
.bar-val{ font-size:9px; color:var(--text-faint); }
.bar{ width:100%; border-radius:6px 6px 0 0; transition:height .5s var(--ease), background var(--fast) var(--ease); }
.bar-today{ background:var(--forest); }
.bar-past{ background:var(--moss-soft); }
.bar-past:hover{ background:var(--moss); }
.bar-day{ font-size:9px; color:var(--text-faint); }
.bar-day-today{ color:var(--forest); font-weight:700; }
.chart-footer{
  border-top:1px solid var(--border-soft);
  padding-top:10px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.muted-text{ font-size:12px; color:var(--text-faint); }
.white-text{ font-size:12.5px; font-weight:700; color:var(--text); }

.trail-title{ font-size:13px; font-weight:600; color:var(--text); margin-bottom:4px; }

.ch-list{ display:flex; flex-direction:column; gap:8px; }
.ch-item{
  display:flex;
  align-items:center;
  gap:10px;
  padding:10px 12px;
  border-radius:var(--r-sm);
  background:var(--bg-input);
  border:1px solid var(--border-soft);
  transition:all var(--fast) var(--ease);
}
.ch-item:hover{ border-color:rgba(45,212,191,0.3); background:rgba(45,212,191,0.06); }
.ch-icon{
  width:30px; height:30px; border-radius:var(--r-sm);
  background:rgba(45,212,191,0.1);
  display:flex; align-items:center; justify-content:center;
  font-size:13px; flex-shrink:0;
}

.link-btn{
  background:transparent;
  border:none;
  color:var(--forest);
  font-size:11.5px;
  font-weight:600;
  cursor:pointer;
  padding:0;
  transition:color var(--fast) var(--ease);
}
.link-btn:hover{ color:var(--forest-bright); }

.lb-user{ display:flex; align-items:center; gap:8px; }
.avatar-sm{ width:26px; height:26px; font-size:11px; }
.points-cell{ font-weight:700; color:var(--amber); }

.empty-state--inline{ max-width:none; padding:28px 16px; margin-bottom:14px; }

@media (max-width:1100px){
  .stats-grid{ grid-template-columns:repeat(2, 1fr); }
  .mid-grid{ grid-template-columns:1fr; }
  .bottom-grid{ grid-template-columns:1fr; }
}
@media (max-width:560px){
  .stats-grid{ grid-template-columns:1fr 1fr; }
}
</style>
