<template>
  <h1>Eco Photos</h1>
  <p class="page-subtitle">Proof of the habits you're sticking to.</p>

  <div class="photo-grid" v-if="photos.length">
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
import { getPhotos } from "../api/photo";

const photos = ref([]);

onMounted(async () => {
  const res = await getPhotos();
  photos.value = res.data.my_eco_photos;
});
</script>
