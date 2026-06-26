<template>
  <div class="page-head">
    <div>
      <span class="eyebrow">{{ challenges.length }} active</span>
      <h1>Challenges</h1>
    </div>
    <router-link to="/challenges/new" class="btn">
      <svg viewBox="0 0 20 20" width="14" height="14" fill="none"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      Create Challenge
    </router-link>
  </div>

  <div class="filter-row">
    <button v-for="option in filters" :key="option.value" class="btn-sm btn-ghost" :class="{ active: filter === option.value }" @click="setFilter(option.value)">
      {{ option.label }}
    </button>
  </div>

  <div v-if="error" class="error-banner">{{ error }}</div>
  <div v-if="message" class="success-banner">{{ message }}</div>
  <div v-if="loading" class="loading-state">Loading challenges...</div>

  <div v-else-if="challenges.length">
    <div v-for="c in challenges" :key="c.id" class="card">
      <div class="card-row">
        <h3>{{ c.title }}</h3>
        <span class="tag">{{ c.has_joined ? "Joined" : c.target_type }}</span>
      </div>
      <p style="color: var(--color-text-muted); font-size: 13.5px; margin: 6px 0 0;">{{ c.description }}</p>
      <div class="trail-progress" style="margin:14px 0 4px;">
        <div class="trail-progress-fill" :style="{ width: `${Math.min(c.group_progress_percent || 0, 100)}%` }"></div>
      </div>
      <p class="hint-text">{{ c.group_progress_percent || 0 }}% community progress</p>

      <div class="card-actions">
        <button class="btn-sm" :disabled="c.has_joined" @click="join(c.id)">
          {{ c.has_joined ? "Joined" : "Join" }}
        </button>
        <button class="btn-danger btn-sm" @click="remove(c.id)">Delete</button>
      </div>
    </div>
  </div>

  <div v-else class="empty-state">
    <div class="empty-icon">🚩</div>
    No challenges yet — start one and invite your friends.
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { getChallenges, joinChallenge, deleteChallenge } from "../api/challenge";
import { apiErrorMessage } from "../api/client";

const challenges = ref([]);
const loading = ref(true);
const error = ref("");
const message = ref("");
const filter = ref("all");
const filters = [
  { label: "All", value: "all" },
  { label: "Joined", value: "joined" },
  { label: "Active", value: "active" },
  { label: "Completed", value: "completed" },
];

async function load() {
  loading.value = true;
  error.value = "";
  try {
    const res = await getChallenges(filter.value);
    challenges.value = res.data ?? [];
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not load challenges.");
  } finally {
    loading.value = false;
  }
}

async function join(id) {
  error.value = "";
  message.value = "";
  try {
    await joinChallenge(id);
    message.value = "Challenge joined.";
    await load();
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not join this challenge.");
  }
}

async function remove(id) {
  error.value = "";
  message.value = "";
  try {
    await deleteChallenge(id);
    message.value = "Challenge deleted.";
    await load();
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not delete this challenge.");
  }
}

function setFilter(value) {
  filter.value = value;
  load();
}

onMounted(load);
</script>

<style scoped>
.filter-row{
  display:flex;
  gap:8px;
  margin-bottom:16px;
  flex-wrap:wrap;
}
.filter-row .active{
  background:var(--moss-soft);
  border-color:var(--border-hover);
  color:var(--forest-bright);
}
</style>
