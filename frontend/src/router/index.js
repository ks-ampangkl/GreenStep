import { createRouter, createWebHistory } from "vue-router";

import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import Dashboard from "../views/Dashboard.vue";
import ActivityList from "../views/ActivityList.vue";
import LogActivity from "../views/LogActivity.vue";
import ChallengeList from "../views/ChallengeList.vue";
import ChallengeCreate from "../views/ChallengeCreate.vue";
import FriendList from "../views/FriendList.vue";
import FriendRequests from "../views/FriendRequests.vue";
import Goal from "../views/Goal.vue";
import PhotoGallery from "../views/PhotoGallery.vue";
import Leaderboard from "../views/Leaderboard.vue";

const routes = [
  { path: "/", component: Dashboard, meta: { requiresAuth: true } },
  { path: "/login", component: Login, meta: { hideLayout: true } },
  { path: "/register", component: Register, meta: { hideLayout: true } },
  { path: "/activities", component: ActivityList, meta: { requiresAuth: true } },
  { path: "/activities/log", component: LogActivity, meta: { requiresAuth: true } },
  { path: "/challenges", component: ChallengeList, meta: { requiresAuth: true } },
  { path: "/challenges/new", component: ChallengeCreate, meta: { requiresAuth: true } },
  { path: "/friends", component: FriendList, meta: { requiresAuth: true } },
  { path: "/friends/requests", component: FriendRequests, meta: { requiresAuth: true } },
  { path: "/goals", component: Goal, meta: { requiresAuth: true } },
  { path: "/photos", component: PhotoGallery, meta: { requiresAuth: true } },
  { path: "/leaderboard", component: Leaderboard, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const token = localStorage.getItem("token");

  if (to.meta.requiresAuth && !token) {
    return { path: "/login", query: { redirect: to.fullPath } };
  }

  if ((to.path === "/login" || to.path === "/register") && token) {
    return "/";
  }
});

export default router;
