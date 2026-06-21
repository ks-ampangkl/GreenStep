<template>
  <h1>My Goal</h1>

  <div class="card" v-if="goal">
    <span class="eyebrow">Current goal</span>
    <h2 style="margin-top:6px;">{{ goal.current_goal.title }}</h2>

    <div class="trail-progress">
      <div class="trail-progress-fill" :style="{ width: goal.progress.progress_percent + '%' }"></div>
      <div class="trail-progress-marker" :style="{ left: goal.progress.progress_percent + '%' }">🐾</div>
    </div>
    <div class="trail-meta">
      <span>Start</span>
      <span class="mono">{{ goal.progress.progress_percent }}%</span>
      <span>Target</span>
    </div>
  </div>

  <div v-else class="empty-state" style="margin-bottom:24px;">
    <div class="empty-icon">🎯</div>
    No goal set yet — set one below to start tracking progress.
  </div>

  <h2>Set a new goal</h2>
  <div class="form-card">
    <form @submit.prevent="save">
      <div class="field">
        <label>Title</label>
        <input v-model="title" placeholder="Cut weekly emissions" required />
      </div>
      <div class="field">
        <label>Target to reduce (kg)</label>
        <input v-model="target" placeholder="e.g. 20" required />
      </div>
      <div class="field">
        <label>Duration</label>
        <input v-model="duration" placeholder="e.g. 30 days" required />
      </div>
      <div class="field">
        <label>Start date</label>
        <input type="date" v-model="start_date" required />
      </div>
      <button class="btn-block">Save Goal</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { getGoal, setGoal } from "../api/goal";

const goal = ref(null);

const title = ref("");
const target = ref("");
const duration = ref("");
const start_date = ref("");

onMounted(async () => {
  const res = await getGoal();
  goal.value = res.data;
});

async function save() {
  await setGoal({
    title: title.value,
    target_to_reduce_kg: parseFloat(target.value),
    duration: duration.value,
    start_date: start_date.value,
  });
  location.reload();
}
</script>
