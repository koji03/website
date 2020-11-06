<template>
    <div class="p-sidenav__wrap__item" @click="pushHome" v-bind:class="{ js_active__color: isHome}">
        <span class="p-sidenav__icon">
                <i class="fas fa-home"></i>
            </span>
            <span class="u-margin__sidenav--right p-sidenav__text" 
            v-bind:class="{ 
                js_sidenav__active: !activeAccountList,
                js_sidenav__error:errorAccountListFlag,
                js_sidenav__hide:!activeAccountList
                    }">
                ホーム
            </span>
        </div>
</template>
<script>
import { xl } from '../../../util'
export default {
    data(){
        return{
            isHome:false,
        }
    },
    props:["width","errorAccountListFlag","activeAccountList"],
    methods:{
        //ホーム画面に遷移する
        pushHome(){
            //画面サイズが一定値以下のときはアカウントリストを閉じる。
            if (this.width <= xl && !this.activeAccountList) {
                this.$store.commit('twitter/isList')
            }
            if(this.$route.path === `/kamitter/home`){
                return false;
            }
            this.$router.push(`/kamitter/home`)
        },
    },
    watch: {
        '$route'(to, from) {
            if(this.$route.path === `/kamitter/home`){
                this.isHome = true
            }else{
                this.isHome = false
            }
        }
    },
    created(){
        if(this.$route.path === `/kamitter/home`){
                this.isHome = true
            }else{
                this.isHome = false
            }
    },
}
</script>