<template>
  <div class="page-head">
    <div>
      <span class="eyebrow">{{ activities.length }} logged</span>
      <h1>Today's Activities</h1>
    </div>
    <router-link to="/activities/log" class="btn">
      <svg viewBox="0 0 20 20" width="14" height="14" fill="none"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      Log Activity
    </router-link>
  </div>

  <div v-if="error" class="error-banner">{{ error }}</div>
  <div v-if="loading" class="loading-state">Loading today’s activity...</div>

  <div v-else-if="activities.length">
    <div v-for="a in activities" :key="a.id" class="card card-row">
      <div>
        <h3>{{ a.activity }}</h3>
        <span class="tag">{{ a.amount }} {{ a.unit }}</span>
      </div>
      <div class="mono" style="font-size:18px; color: var(--color-clay); font-weight:600; white-space:nowrap;">
        {{ a.emissions_kg }} kg
      </div>
    </div>
  </div>

  <div v-else class="empty-state">
    <div class="empty-icon">🌱</div>
    Nothing logged yet today — every habit you track sharpens the picture.
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { getTodayLogs } from "../api/activity";
import { apiErrorMessage } from "../api/client";

const activities = ref([]);
const loading = ref(true);
const error = ref("");

onMounted(async () => {
  try {
    const res = await getTodayLogs();
    activities.value = res.data ?? [];
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not load today’s activities.");
  } finally {
    loading.value = false;
  }
});
</script>
