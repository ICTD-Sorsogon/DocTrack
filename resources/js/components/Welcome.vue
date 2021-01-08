<template>
<v-app>
    <v-scroll-x-transition :hide-on-leave="Boolean(true)">
        <router-view/>
    </v-scroll-x-transition>
</v-app>
</template>

<style>
.home_title_link:link,
.home_title_link:active,
.home_title_link:hover,
.home_title_link:visited {
    text-decoration: none;
    color: white;
}
</style>
