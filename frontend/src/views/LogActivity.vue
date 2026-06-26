<template>
  <h1>Log Activity</h1>
  <p class="page-subtitle">Record something you did so we can track its footprint.</p>

  <div class="form-card">
    <div v-if="error" class="error-banner">{{ error }}</div>
    <div v-if="success" class="success-banner">{{ success }}</div>

    <form @submit.prevent="submit">
      <div class="field">
        <label>Activity type</label>
        <select v-model="activity_type_id" required>
          <option disabled value="">Choose an activity</option>
          <option v-for="type in activityTypes" :key="type.id" :value="type.id">
            {{ type.category }} · {{ type.name }} ({{ type.unit }})
          </option>
        </select>
        <p v-if="selectedType" class="hint-text">
          {{ selectedType.kg_co2_per_unit }} kg CO₂e per {{ selectedType.unit }}. {{ selectedType.info }}
        </p>
      </div>

      <div class="field">
        <label>Amount<span v-if="selectedType"> ({{ selectedType.unit }})</span></label>
        <input v-model="amount" type="number" min="0.01" step="0.01" placeholder="e.g. 12.5" required />
      </div>

      <div class="field">
        <label>Date</label>
        <input v-model="date" type="date" required />
      </div>

      <button class="btn-block" :disabled="saving">
        <svg viewBox="0 0 20 20" width="14" height="14" fill="none"><path d="M4 10.5 8 14l8-9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        {{ saving ? "Saving..." : "Save" }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { getActivityTypes, logActivity } from "../api/activity";
import { apiErrorMessage } from "../api/client";

const router = useRouter();

const activity_type_id = ref("");
const amount = ref("");
const date = ref(new Date().toISOString().slice(0, 10));
const activityTypes = ref([]);
const saving = ref(false);
const error = ref("");
const success = ref("");

const selectedType = computed(() =>
  activityTypes.value.find((type) => String(type.id) === String(activity_type_id.value))
);

onMounted(async () => {
  try {
    const res = await getActivityTypes();
    activityTypes.value = res.data ?? [];
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not load activity types.");
  }
});

async function submit() {
  saving.value = true;
  error.value = "";
  success.value = "";

  try {
    const res = await logActivity({
      activity_type_id: parseInt(activity_type_id.value, 10),
      amount: parseFloat(amount.value),
      date: date.value,
    });

    success.value = res.data?.message || "Activity saved.";
    router.push("/activities");
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not save this activity.");
  } finally {
    saving.value = false;
  }
}
</script>
