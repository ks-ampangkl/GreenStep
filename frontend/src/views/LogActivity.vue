<template>
  <h1>Log Activity</h1>
  <p class="page-subtitle">Record something you did so we can track its footprint.</p>

  <div class="form-card">
    <form @submit.prevent="submit">
      <div class="field">
        <label>Activity Type ID</label>
        <input v-model="activity_type_id" placeholder="e.g. 4" required />
      </div>

      <div class="field">
        <label>Amount</label>
        <input v-model="amount" placeholder="e.g. 12.5" required />
      </div>

      <div class="field">
        <label>Date</label>
        <input v-model="date" type="date" required />
      </div>

      <button class="btn-block">
        <svg viewBox="0 0 20 20" width="14" height="14" fill="none"><path d="M4 10.5 8 14l8-9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Save
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { logActivity } from "../api/activity";

const activity_type_id = ref("");
const amount = ref("");
const date = ref("");

async function submit() {
  await logActivity({
    activity_type_id: parseInt(activity_type_id.value),
    amount: parseFloat(amount.value),
    date: date.value,
  });
  alert("Saved");
}
</script>
