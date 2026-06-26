<template>
  <h1>Eco Photos</h1>
  <p class="page-subtitle">Proof of the habits you're sticking to.</p>

  <div class="form-card compact-form">
    <div v-if="error" class="error-banner">{{ error }}</div>
    <div v-if="message" class="success-banner">{{ message }}</div>
    <form @submit.prevent="submit">
      <div class="field">
        <label>Image URL</label>
        <input v-model="image_url" type="url" placeholder="https://example.com/photo.jpg" required />
      </div>
      <div class="field">
        <label>Achievement</label>
        <input v-model="achievement" placeholder="Biked to campus for a week" required />
      </div>
      <button class="btn-block" :disabled="saving">{{ saving ? "Uploading..." : "Add Photo" }}</button>
    </form>
  </div>

  <div v-if="loading" class="loading-state">Loading photos...</div>

  <div class="photo-grid" v-else-if="photos.length">
    <div v-for="p in photos" :key="p.id" class="photo-card">
      <img :src="p.image_url" :alt="p.achievement" />
      <div class="photo-caption">
        <span>🌿</span> {{ p.achievement }}
      </div>
    </div>
  </div>

  <div v-else class="empty-state">
    <div class="empty-icon">📸</div>
    No photos yet — share proof of an eco win to add one here.
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { getPhotos, uploadPhoto } from "../api/photo";
import { apiErrorMessage } from "../api/client";

const photos = ref([]);
const image_url = ref("");
const achievement = ref("");
const loading = ref(true);
const saving = ref(false);
const error = ref("");
const message = ref("");

async function load() {
  loading.value = true;
  error.value = "";
  try {
    const res = await getPhotos();
    photos.value = res.data.my_eco_photos ?? [];
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not load photos.");
  } finally {
    loading.value = false;
  }
}

async function submit() {
  saving.value = true;
  error.value = "";
  message.value = "";

  try {
    const res = await uploadPhoto({
      image_url: image_url.value,
      achievement: achievement.value,
    });
    image_url.value = "";
    achievement.value = "";
    message.value = res.data?.message || "Photo added.";
    await load();
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not upload this photo.");
  } finally {
    saving.value = false;
  }
}

onMounted(load);
</script>

<style scoped>
.compact-form{
  max-width:560px;
  margin-bottom:20px;
}
</style>
