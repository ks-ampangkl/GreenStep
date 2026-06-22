
<template>

<Navbar />


<div class="container">


<Sidebar />


<div class="content">


<router-view />


</div>


</div>


</template>



<script setup>


import Navbar

from "../components/Navbar.vue";


import Sidebar

from "../components/Sidebar.vue";


</script>
