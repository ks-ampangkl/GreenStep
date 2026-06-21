<template>
  <h1>Create Challenge</h1>
  <p class="page-subtitle">Set a shared goal you and your friends can join.</p>

  <div class="form-card">
    <form @submit.prevent="submit">
      <div class="field">
        <label>Title</label>
        <input v-model="title" placeholder="No-car November" required />
      </div>

      <div class="field">
        <label>Description</label>
        <input v-model="description" placeholder="Skip the car for the whole month" />
      </div>

      <div class="field">
        <label>Target type</label>
        <input v-model="target_type" placeholder="transport / food / energy" required />
      </div>

      <button class="btn-block">Create</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { createChallenge } from "../api/challenge";

const router = useRouter();

const title = ref("");
const description = ref("");
const target_type = ref("");

async function submit() {
  await createChallenge({
    title: title.value,
    description: description.value,
    target_type: target_type.value,
  });
  router.push("/challenges");
}
</script>
