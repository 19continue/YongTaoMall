<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>
import Home from '@/layout/Home.vue';
export default {
  name: 'App',
  components: {
    Home
  }
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
}

#app {
  background-color: whitesmoke;
}
</style>
