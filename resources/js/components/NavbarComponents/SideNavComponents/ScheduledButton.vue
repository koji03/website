<template>
    <div class="p-sidenav__wrap__item" @click="changeModal"
    v-bind:class="{ js_active__color: isModalShow}">
        <span class="p-sidenav__icon">
            <i class="fas fa-comment-dots"></i>
        </span>
        <span class="u-margin__sidenav--right p-sidenav__text" 
        v-bind:class="{ js_sidenav__active: !activeAccountList,
        js_sidenav__hide:!activeAccountList }">
            予約ツイート
        </span>
    </div>
</template>
<script>
import { xl } from '../../../util'
export default {
    props:["width","isModalShow","activeAccountList"],
    methods:{
        //予約ツイート画面を表示する。
        changeModal(){
            //画面サイズが一定値以下のときはアカウントリストを閉じる。
            if (this.width <= xl && !this.activeAccountList) {
                this.$store.commit('twitter/isList')
            }
            this.$store.commit('modal/isModalShow');
        },
    }
}
</script>