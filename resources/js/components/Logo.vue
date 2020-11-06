<template>
    <div class="p-logo">
        <div class="p-logo__icon" @click="isActiveChange" >
            <i class="fas fa-list " v-if="isLogin"></i>
        </div>

        <span v-bind:class="{
            js_sidenav__show:activeAccountList,
            js_logo_padding__left:isLogin,
            js_sidenav__hide:!activeAccountList,
            }">
            <a href="/" class="p-logo__title" >
                {{ siteTitle }}
            </a>
        </span>
 
    </div>
</template>

<script>
import { mapState,mapGetters } from 'vuex'
export default {
    methods:{
        //sideバーの出し入れ
        isActiveChange(){
            this.$store.commit('twitter/isList')
        },
    },
    computed: {
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
            activeAccountList: state => state.twitter.activeAccountList,
            siteTitle: state => state.twitter.siteTitle,
        }),
        ...mapGetters({
            isLogin: 'auth/check'
        })
    },
}
</script>