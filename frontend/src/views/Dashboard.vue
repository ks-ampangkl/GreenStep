<template>
  <div class="dash-root">

    <!-- ════════════════════════════════════════
         TOP NAV
    ════════════════════════════════════════ -->
    <!-- <nav class="nav">
      <div class="nav-inner">
        <div class="nav-logo">
          <span class="nav-logo-icon">🌿</span>
          <span class="nav-logo-text">GreenStep</span>
        </div>
        <div class="nav-right">
          <button class="nav-icon-btn" aria-label="Notifications">🔔</button>
          <button class="nav-icon-btn" aria-label="Settings" @click="$emit('go-settings')">⚙️</button>
          <div class="nav-avatar">{{ user?.name?.charAt(0)?.toUpperCase() ?? 'U' }}</div>
        </div>
      </div>
    </nav> -->

    <!-- ════════════════════════════════════════
         MAIN CONTENT
    ════════════════════════════════════════ -->
    <main class="main">

      <!-- Page header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Good {{ timeOfDay }}, {{ user?.name ?? 'there' }} 👋</h1>
          <p class="page-sub">Here's your carbon impact at a glance.</p>
        </div>
        <button class="btn-primary" @click="$emit('open-log')">
          <span>+</span> Log Activity
        </button>
      </div>

      <!-- ── STAT CARDS ── -->
      <div class="stats-grid">

        <div class="stat-card">
          <div class="stat-head">
            <span class="stat-label">CO₂ Saved</span>
            <span class="stat-icon green">🌿</span>
          </div>
          <div class="stat-value">
            {{ dashboard?.total_co2_saved ?? '0' }}
            <span class="stat-unit">kg</span>
          </div>
          <p class="stat-trend green-text">↑ {{ dashboard?.co2_change_pct ?? 0 }}% this week</p>
        </div>

        <div class="stat-card">
          <div class="stat-head">
            <span class="stat-label">Points</span>
            <span class="stat-icon amber">⭐</span>
          </div>
          <div class="stat-value">{{ dashboard?.total_points?.toLocaleString() ?? '0' }}</div>
          <p class="stat-trend muted">Lifetime eco points</p>
        </div>

        <div class="stat-card">
          <div class="stat-head">
            <span class="stat-label">Streak</span>
            <span class="stat-icon orange">⚡</span>
          </div>
          <div class="stat-value">
            {{ dashboard?.current_streak ?? 0 }}
            <span class="stat-unit">days</span>
          </div>
          <p class="stat-trend muted">Best: {{ dashboard?.best_streak ?? 0 }} days</p>
        </div>

        <div class="stat-card">
          <div class="stat-head">
            <span class="stat-label">Rank</span>
            <span class="stat-icon purple">📊</span>
          </div>
          <div class="stat-value">#{{ dashboard?.leaderboard_rank ?? '–' }}</div>
          <p class="stat-trend muted">Among {{ dashboard?.total_users ?? 0 }} users</p>
        </div>

      </div>

      <!-- ── MID ROW: Today log + Weekly chart ── -->
      <div class="mid-grid">

        <!-- Today's log -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Today's log</span>
            <span class="tag green-tag">{{ todayActivities?.length ?? 0 }} logged</span>
          </div>

          <div class="act-list">
            <template v-if="todayActivities?.length">
              <div
                v-for="act in todayActivities"
                :key="act.id"
                class="act-item"
              >
                <div class="act-icon">{{ activityEmoji(act.type) }}</div>
                <div class="act-info">
                  <p class="act-name">{{ act.description }}</p>
                  <p class="act-type">{{ act.type }}</p>
                </div>
                <div class="act-right">
                  <p class="act-co2">-{{ act.carbon_saved }} kg</p>
                  <p class="act-pts">+{{ act.points }} pts</p>
                </div>
              </div>
            </template>
            <div v-else class="empty-state">
              <span class="empty-icon">📭</span>
              <p>No activities yet today</p>
            </div>
          </div>

          <button class="btn-outline w-full mt-12" @click="$emit('open-log')">
            + Log an activity
          </button>
        </div>

        <!-- Weekly chart -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">CO₂ saved this week</span>
            <span class="chart-legend">
              <span class="legend-dot"></span> kg CO₂
            </span>
          </div>

          <div class="chart-bars">
            <div
              v-for="(day, i) in weeklyData"
              :key="i"
              class="bar-col"
            >
              <span class="bar-val">{{ day.value }}</span>
              <div
                class="bar"
                :class="day.isToday ? 'bar-today' : 'bar-past'"
                :style="{ height: barHeight(day.value) }"
              ></div>
              <span class="bar-day" :class="day.isToday ? 'bar-day-today' : ''">{{ day.label }}</span>
            </div>
          </div>

          <div class="chart-footer">
            <span class="muted-text">Weekly total</span>
            <span class="white-text fw-600">{{ weeklyTotal }} kg CO₂</span>
          </div>
        </div>

      </div>

      <!-- ── BOTTOM ROW: Goals + Challenges + Tip ── -->
      <div class="bottom-grid">

        <!-- Goals -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">My goals</span>
            <button class="icon-btn" @click="$emit('edit-goals')">✎</button>
          </div>

          <template v-if="goals?.daily_co2_goal || goals?.weekly_points_goal">
            <div v-if="goals?.daily_co2_goal" class="prog-block">
              <div class="prog-labels">
                <span class="muted-text">Daily CO₂ target</span>
                <span class="white-text">{{ todayCO2 }} / {{ goals.daily_co2_goal }} kg</span>
              </div>
              <div class="prog-track">
                <div
                  class="prog-fill green-fill"
                  :style="{ width: Math.min((todayCO2 / goals.daily_co2_goal) * 100, 100) + '%' }"
                ></div>
              </div>
            </div>

            <div v-if="goals?.weekly_points_goal" class="prog-block">
              <div class="prog-labels">
                <span class="muted-text">Weekly points</span>
                <span class="white-text">{{ weeklyPoints }} / {{ goals.weekly_points_goal }} pts</span>
              </div>
              <div class="prog-track">
                <div
                  class="prog-fill amber-fill"
                  :style="{ width: Math.min((weeklyPoints / goals.weekly_points_goal) * 100, 100) + '%' }"
                ></div>
              </div>
            </div>
          </template>

          <div v-else class="empty-state">
            <span class="empty-icon">🎯</span>
            <p>No goals set yet</p>
            <button class="btn-outline mt-8" @click="$emit('edit-goals')">Set a goal</button>
          </div>
        </div>

        <!-- Challenges -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Challenges</span>
            <button class="link-btn" @click="$emit('go-challenges')">Browse →</button>
          </div>

          <template v-if="challenges?.length">
            <div
              v-for="ch in challenges.slice(0, 3)"
              :key="ch.id"
              class="ch-item"
            >
              <div class="ch-icon">🏆</div>
              <div class="ch-info">
                <p class="act-name">{{ ch.title }}</p>
                <p class="act-type">{{ ch.participants_count }} participants</p>
              </div>
              <span class="tag teal-tag">{{ ch.status ?? 'active' }}</span>
            </div>
          </template>
          <div v-else class="empty-state">
            <span class="empty-icon">🏆</span>
            <p>No active challenges</p>
          </div>
        </div>

        <!-- Eco tip -->
        <div class="card tip-card">
          <div class="tip-header">
            <span class="tip-icon">💡</span>
            <span class="stat-label">Eco tip</span>
          </div>
          <blockquote class="tip-text">
            "{{ currentTip?.tip ?? 'Loading tip…' }}"
          </blockquote>
          <span v-if="currentTip?.category" class="tag green-tag mt-10">{{ currentTip.category }}</span>
          <button class="link-btn mt-14" @click="$emit('next-tip')">↻ Next tip</button>
        </div>

      </div>

      <!-- ── LEADERBOARD ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Leaderboard</span>
          <button class="link-btn" @click="$emit('go-leaderboard')">View all →</button>
        </div>

        <div class="lb-wrap">
          <table class="lb-table">
            <thead>
              <tr>
                <th class="th">#</th>
                <th class="th" style="text-align:left">User</th>
                <th class="th">CO₂ saved</th>
                <th class="th">Points</th>
                <th class="th">Streak</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(entry, i) in leaderboard?.slice(0, 5)"
                :key="entry.user_id"
                class="lb-row"
                :class="entry.user_id === user?.id ? 'lb-me' : ''"
              >
                <td class="td">
                  <span class="rank-badge" :class="rankClass(i)">{{ i + 1 }}</span>
                </td>
                <td class="td">
                  <div class="lb-user">
                    <div class="lb-avatar">{{ entry.username?.charAt(0)?.toUpperCase() ?? '?' }}</div>
                    <span :class="entry.user_id === user?.id ? 'green-text fw-600' : 'white-text'">
                      {{ entry.username }}
                      <span v-if="entry.user_id === user?.id" class="muted-text fw-400"> (you)</span>
                    </span>
                  </div>
                </td>
                <td class="td" style="text-align:right; color:#94a3b8">{{ entry.total_co2_saved }} kg</td>
                <td class="td" style="text-align:right; color:#fbbf24; font-weight:600">{{ entry.total_points?.toLocaleString() }}</td>
                <td class="td" style="text-align:right; color:#64748b">{{ entry.current_streak }} d</td>
              </tr>

              <tr v-if="!leaderboard?.length">
                <td colspan="5" class="lb-empty">No data yet</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</template>

<style scoped>
/* ──────────────────────────────────────
   ROOT & RESET
────────────────────────────────────── */
.dash-root {
  min-height: 100vh;
  background: #0a0f0a;
  color: #e2e8f0;
  font-family: 'Inter', system-ui, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
}

/* ──────────────────────────────────────
   NAV
────────────────────────────────────── */
.nav {
  position: sticky;
  top: 0;
  z-index: 50;
  background: rgba(13, 19, 13, 0.92);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255,255,255,0.05);
}
.nav-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.nav-logo {
  display: flex;
  align-items: center;
  gap: 10px;
}
.nav-logo-icon { font-size: 20px; }
.nav-logo-text {
  font-size: 15px;
  font-weight: 700;
  color: #fff;
  letter-spacing: -0.02em;
}
.nav-right {
  display: flex;
  align-items: center;
  gap: 8px;
}
.nav-icon-btn {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: transparent;
  border: none;
  font-size: 15px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}
.nav-icon-btn:hover { background: rgba(52,211,153,0.12); }
.nav-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(52,211,153,0.18);
  color: #6ee7b7;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: box-shadow 0.2s;
}
.nav-avatar:hover { box-shadow: 0 0 0 2px rgba(52,211,153,0.4); }

/* ──────────────────────────────────────
   MAIN
────────────────────────────────────── */
.main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 32px 24px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* ──────────────────────────────────────
   PAGE HEADER
────────────────────────────────────── */
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}
.page-title {
  font-size: 22px;
  font-weight: 700;
  color: #fff;
  letter-spacing: -0.02em;
}
.page-sub {
  font-size: 13px;
  color: #64748b;
  margin-top: 4px;
}

/* ──────────────────────────────────────
   BUTTONS
────────────────────────────────────── */
.btn-primary {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #10b981;
  color: #000;
  border: none;
  border-radius: 12px;
  padding: 10px 18px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
  white-space: nowrap;
}
.btn-primary:hover {
  background: #34d399;
  box-shadow: 0 0 20px rgba(16,185,129,0.3);
}
.btn-primary:active { transform: scale(0.97); }

.btn-outline {
  background: transparent;
  border: 1px solid rgba(52,211,153,0.3);
  color: #34d399;
  border-radius: 10px;
  padding: 9px 14px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  display: block;
}
.btn-outline:hover { background: rgba(52,211,153,0.08); }
.btn-outline.w-full { width: 100%; text-align: center; }

.link-btn {
  background: transparent;
  border: none;
  color: #34d399;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
  transition: color 0.2s;
}
.link-btn:hover { color: #6ee7b7; }

.icon-btn {
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 14px;
  cursor: pointer;
  padding: 2px 6px;
  border-radius: 6px;
  transition: all 0.2s;
}
.icon-btn:hover { color: #34d399; background: rgba(52,211,153,0.1); }

/* ──────────────────────────────────────
   STAT CARDS
────────────────────────────────────── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
}
.stat-card {
  background: #111711;
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 16px;
  padding: 18px;
  transition: border-color 0.2s, transform 0.2s;
  cursor: default;
}
.stat-card:hover {
  border-color: rgba(52,211,153,0.2);
  transform: translateY(-1px);
}
.stat-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}
.stat-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #64748b;
  font-weight: 600;
}
.stat-icon {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  transition: filter 0.2s;
}
.stat-card:hover .stat-icon { filter: brightness(1.15); }
.stat-icon.green  { background: rgba(52,211,153,0.12); }
.stat-icon.amber  { background: rgba(251,191,36,0.12); }
.stat-icon.orange { background: rgba(249,115,22,0.12); }
.stat-icon.purple { background: rgba(167,139,250,0.12); }

.stat-value {
  font-size: 26px;
  font-weight: 700;
  color: #fff;
  line-height: 1;
  letter-spacing: -0.02em;
}
.stat-unit {
  font-size: 13px;
  font-weight: 400;
  color: #64748b;
  margin-left: 3px;
}
.stat-trend {
  font-size: 11px;
  margin-top: 6px;
  display: flex;
  align-items: center;
  gap: 3px;
}
.green-text { color: #34d399; }
.muted { color: #475569; }

/* ──────────────────────────────────────
   CARDS
────────────────────────────────────── */
.card {
  background: #111711;
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 16px;
  padding: 20px 22px;
  transition: border-color 0.2s;
}
.card:hover { border-color: rgba(52,211,153,0.1); }

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}
.card-title {
  font-size: 14px;
  font-weight: 700;
  color: #fff;
}

/* ──────────────────────────────────────
   TAGS
────────────────────────────────────── */
.tag {
  display: inline-block;
  font-size: 10px;
  font-weight: 600;
  padding: 3px 9px;
  border-radius: 6px;
  border: 1px solid transparent;
}
.green-tag {
  background: rgba(52,211,153,0.1);
  color: #34d399;
  border-color: rgba(52,211,153,0.2);
}
.teal-tag {
  background: rgba(45,212,191,0.1);
  color: #2dd4bf;
  border-color: rgba(45,212,191,0.2);
}
.mt-10 { margin-top: 10px; }

/* ──────────────────────────────────────
   ACTIVITY LIST
────────────────────────────────────── */
.mid-grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 16px;
}
.act-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 260px;
  overflow-y: auto;
  padding-right: 4px;
}
.act-list::-webkit-scrollbar { width: 4px; }
.act-list::-webkit-scrollbar-track { background: transparent; }
.act-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 99px; }
.act-list::-webkit-scrollbar-thumb:hover { background: rgba(52,211,153,0.25); }

.act-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 12px;
  background: rgba(255,255,255,0.025);
  border: 1px solid rgba(255,255,255,0.05);
  transition: all 0.2s;
}
.act-item:hover {
  border-color: rgba(52,211,153,0.2);
  background: rgba(52,211,153,0.04);
}
.act-icon {
  width: 34px;
  height: 34px;
  border-radius: 9px;
  background: rgba(52,211,153,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  flex-shrink: 0;
}
.act-info { flex: 1; min-width: 0; }
.act-name {
  font-size: 12px;
  font-weight: 600;
  color: #e2e8f0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.act-type {
  font-size: 10px;
  color: #475569;
  margin-top: 1px;
  text-transform: capitalize;
}
.act-right { text-align: right; flex-shrink: 0; }
.act-co2 {
  font-size: 12px;
  font-weight: 700;
  color: #34d399;
}
.act-pts {
  font-size: 10px;
  color: #475569;
}
.mt-12 { margin-top: 12px; }

/* ──────────────────────────────────────
   WEEKLY CHART
────────────────────────────────────── */
.chart-legend {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: #475569;
}
.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #34d399;
  display: inline-block;
}
.chart-bars {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  height: 130px;
}
.bar-col {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}
.bar-val {
  font-size: 9px;
  color: #475569;
}
.bar {
  width: 100%;
  border-radius: 6px 6px 0 0;
  transition: height 0.5s ease, background 0.2s;
  cursor: default;
}
.bar-today { background: #34d399; }
.bar-past  { background: rgba(52,211,153,0.22); }
.bar-past:hover { background: rgba(52,211,153,0.45); }
.bar-day {
  font-size: 9px;
  color: #475569;
}
.bar-day-today {
  color: #34d399;
  font-weight: 700;
}
.chart-footer {
  border-top: 1px solid rgba(255,255,255,0.05);
  margin-top: 12px;
  padding-top: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.muted-text  { font-size: 12px; color: #475569; }
.white-text  { font-size: 12px; color: #e2e8f0; }
.fw-600 { font-weight: 700; }
.fw-400 { font-weight: 400; }

/* ──────────────────────────────────────
   BOTTOM GRID
────────────────────────────────────── */
.bottom-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

/* Progress bars */
.prog-block { margin-bottom: 14px; }
.prog-labels {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  margin-bottom: 6px;
}
.prog-track {
  background: rgba(255,255,255,0.06);
  border-radius: 99px;
  height: 6px;
  overflow: hidden;
}
.prog-fill {
  height: 100%;
  border-radius: 99px;
  transition: width 0.6s ease;
}
.green-fill { background: #34d399; }
.amber-fill { background: #fbbf24; }

/* Challenges */
.ch-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 12px;
  background: rgba(255,255,255,0.025);
  border: 1px solid rgba(255,255,255,0.05);
  margin-bottom: 8px;
  transition: all 0.2s;
}
.ch-item:hover {
  border-color: rgba(45,212,191,0.2);
  background: rgba(45,212,191,0.04);
}
.ch-icon {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  background: rgba(45,212,191,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  flex-shrink: 0;
}
.ch-info { flex: 1; min-width: 0; }

/* Eco tip */
.tip-card { position: relative; overflow: hidden; }
.tip-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}
.tip-icon {
  width: 26px;
  height: 26px;
  background: rgba(52,211,153,0.12);
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
}
.tip-text {
  font-size: 13px;
  line-height: 1.65;
  color: #cbd5e1;
  font-style: italic;
}
.mt-8  { margin-top: 8px; }
.mt-14 { margin-top: 14px; }

/* Empty state */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 32px 0;
  gap: 8px;
  color: #475569;
  font-size: 12px;
}
.empty-icon { font-size: 26px; }

/* ──────────────────────────────────────
   LEADERBOARD
────────────────────────────────────── */
.lb-wrap { overflow-x: auto; }
.lb-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
}
.th {
  color: #475569;
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  font-weight: 600;
  padding-bottom: 10px;
  text-align: right;
}
.th:first-child { text-align: left; }
.th:nth-child(2) { text-align: left; }
.td {
  padding: 9px 0;
  border-top: 1px solid rgba(255,255,255,0.04);
  text-align: right;
}
.td:first-child { text-align: left; }
.td:nth-child(2) { text-align: left; }

.lb-row { transition: background 0.15s; }
.lb-row:hover { background: rgba(255,255,255,0.02); }
.lb-me { background: rgba(52,211,153,0.04); }

.rank-badge {
  width: 24px;
  height: 24px;
  border-radius: 7px;
  font-size: 11px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.rank-gold   { background: rgba(251,191,36,0.15); color: #fbbf24; }
.rank-silver { background: rgba(148,163,184,0.12); color: #94a3b8; }
.rank-bronze { background: rgba(249,115,22,0.12); color: #fb923c; }
.rank-n      { background: rgba(255,255,255,0.04); color: #475569; }

.lb-user {
  display: flex;
  align-items: center;
  gap: 8px;
}
.lb-avatar {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: rgba(52,211,153,0.12);
  color: #6ee7b7;
  font-size: 10px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.lb-empty {
  text-align: center;
  padding: 32px 0;
  color: #475569;
}

/* ──────────────────────────────────────
   RESPONSIVE
────────────────────────────────────── */
@media (max-width: 900px) {
  .stats-grid   { grid-template-columns: repeat(2, 1fr); }
  .mid-grid     { grid-template-columns: 1fr; }
  .bottom-grid  { grid-template-columns: 1fr; }
}
@media (max-width: 500px) {
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .main { padding: 20px 16px; gap: 16px; }
}
</style>

<script setup>
/**
 * Dashboard.vue — script setup stub
 *
 * All data is injected via props from the parent (App.vue / router view).
 * The computed helpers (timeOfDay, weeklyTotal, weeklyData, barHeight,
 * activityEmoji, rankClass) live here so the template stays clean.
 *
 * ── PROPS ─────────────────────────────────────────────────────────────────
 */
import { computed } from 'vue'

const props = defineProps({
  user:             { type: Object,  default: null  },
  dashboard:        { type: Object,  default: null  },
  todayActivities:  { type: Array,   default: () => [] },
  leaderboard:      { type: Array,   default: () => [] },
  challenges:       { type: Array,   default: () => [] },
  goals:            { type: Object,  default: null  },
  currentTip:       { type: Object,  default: null  },
  todayCO2:         { type: Number,  default: 0     },
  weeklyPoints:     { type: Number,  default: 0     },
})

// ── EMITS ──────────────────────────────────────────────────────────────────
defineEmits(['open-log', 'edit-goals', 'go-challenges', 'go-leaderboard', 'go-settings', 'next-tip'])

// ── COMPUTED ───────────────────────────────────────────────────────────────

/** Greeting based on current hour */
const timeOfDay = computed(() => {
  const h = new Date().getHours()
  if (h < 12) return 'morning'
  if (h < 17) return 'afternoon'
  return 'evening'
})

/**
 * Build the 7-day bar-chart dataset.
 * Falls back to zeros so the chart always renders.
 */
const weeklyData = computed(() => {
  const days  = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  const today = new Date().getDay()               // 0=Sun … 6=Sat
  const todayIdx = today === 0 ? 6 : today - 1   // shift to Mon=0 … Sun=6

  const raw = props.dashboard?.weekly_breakdown ?? []
  return days.map((label, i) => ({
    label,
    value:   raw[i] ?? 0,
    isToday: i === todayIdx,
  }))
})

/** Sum of the weekly chart values */
const weeklyTotal = computed(() =>
  weeklyData.value.reduce((s, d) => s + (d.value ?? 0), 0).toFixed(1)
)

/** Scale each bar to a max height of 120 px */
const barHeight = (value) => {
  const max = Math.max(...weeklyData.value.map(d => d.value), 1)
  const px  = Math.round((value / max) * 120)
  return `${px}px`
}

/** Map activity type strings → a relevant emoji */
const activityEmoji = (type) => {
  const map = {
    transport: '🚲', cycling: '🚲', walking: '🚶', commute: '🚌',
    food: '🥦', meal: '🌱', diet: '🥗',
    energy: '💡', electricity: '⚡', solar: '☀️',
    waste: '♻️', recycling: '♻️',
    shopping: '🛍️', water: '💧',
  }
  const key = (type ?? '').toLowerCase()
  return map[key] ?? map[Object.keys(map).find(k => key.includes(k))] ?? '🌿'
}

/** CSS class for leaderboard rank badge */
const rankClass = (i) => {
  if (i === 0) return 'rank-gold'
  if (i === 1) return 'rank-silver'
  if (i === 2) return 'rank-bronze'
  return 'rank-n'
}
</script>
