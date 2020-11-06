<template>
    <div class="l-header u-margin__top">
        <SideNav/>
        <div class="p-header">
            <Logo/>
            <nav class="p-header__nav">
                <span v-if="!isLogin">
                    <navNotLogin/>
                </span>
                <span  v-if="isLogin">
                    <navLogin/>
                </span>
            </nav>
        </div>
    </div>
</template>
<script>
import { mapState, mapGetters } from 'vuex'
import SideNav from './../NavbarComponents/SideNav'
import navLogin from './NavLogin'
import navNotLogin from './NavNotLogin'
import Logo from '../Logo'
export default {
    data(){
        return{
            Panel:false,
        }
    },
    components:{
        SideNav,
        navLogin,
        navNotLogin,
        Logo
    },
  computed: {
    ...mapGetters({
      isLogin: 'auth/check'
    })
  },
}
</script>