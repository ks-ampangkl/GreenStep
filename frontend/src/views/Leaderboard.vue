<template>
  <h1>Leaderboard</h1>
  <p class="page-subtitle">Ranked by total eco points this season.</p>

  <div v-if="error" class="error-banner">{{ error }}</div>
  <div v-if="loading" class="loading-state">Loading leaderboard...</div>

  <table v-else-if="users.length">
    <tr>
      <th>Rank</th>
      <th>Name</th>
      <th>Points</th>
    </tr>
    <tr v-for="(u, i) in users" :key="u.id">
      <td class="rank-cell">{{ medal(i) }}</td>
      <td>
        <div style="display:flex; align-items:center; gap:10px;">
          <div class="avatar" style="width:30px;height:30px;font-size:12px;">{{ initials(u.name) }}</div>
          {{ u.name }}
        </div>
      </td>
      <td class="mono" style="font-weight:600; color: var(--color-forest);">{{ u.eco_points }}</td>
    </tr>
  </table>

  <div v-else class="empty-state">
    <div class="empty-icon">🏆</div>
    The leaderboard is empty for now.
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { getLeaderboard } from "../api/activity";
import { apiErrorMessage } from "../api/client";

const users = ref([]);
const loading = ref(true);
const error = ref("");

function initials(name) {
  return (name || "")
    .split(" ")
    .map((p) => p[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();
}

function medal(i) {
  return i === 0 ? "🥇" : i === 1 ? "🥈" : i === 2 ? "🥉" : `#${i + 1}`;
}

onMounted(async () => {
  try {
    const res = await getLeaderboard();
    users.value = res.data.leaderboard ?? [];
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not load the leaderboard.");
  } finally {
    loading.value = false;
  }
});
</script>
