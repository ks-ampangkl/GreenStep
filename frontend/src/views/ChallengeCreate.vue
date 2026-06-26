<template>
  <h1>Create Challenge</h1>
  <p class="page-subtitle">Set a shared goal you and your friends can join.</p>

  <div class="form-card">
    <div v-if="error" class="error-banner">{{ error }}</div>

    <form @submit.prevent="submit">
      <div class="field">
        <label>Title</label>
        <input v-model="title" placeholder="No-car November" required />
      </div>

      <div class="field">
        <label>Description</label>
        <input v-model="description" placeholder="Skip the car for the whole month" required />
      </div>

      <div class="field">
        <label>Target type</label>
        <select v-model="target_type" required>
          <option disabled value="">Choose a target</option>
          <option value="transport">Transport</option>
          <option value="food">Food</option>
          <option value="energy">Energy</option>
          <option value="waste">Waste</option>
        </select>
      </div>

      <button class="btn-block" :disabled="saving">{{ saving ? "Creating..." : "Create" }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { createChallenge } from "../api/challenge";
import { apiErrorMessage } from "../api/client";

const router = useRouter();

const title = ref("");
const description = ref("");
const target_type = ref("");
const saving = ref(false);
const error = ref("");

async function submit() {
  saving.value = true;
  error.value = "";

  try {
    await createChallenge({
      title: title.value,
      description: description.value,
      target_type: target_type.value,
    });
    router.push("/challenges");
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not create this challenge.");
  } finally {
    saving.value = false;
  }
}
</script>
