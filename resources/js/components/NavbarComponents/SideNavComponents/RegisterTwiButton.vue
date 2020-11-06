<template>
    <div class="p-sidenav__wrap__item"  @click="twitterRegister">
        <span class="p-sidenav__icon">
            <i class="fas fa-plus-circle"></i>
        </span>
        <span class="u-margin__sidenav--right p-sidenav__text" 
        v-bind:class="{ 
            js_sidenav__active: !activeAccountList,
            js_sidenav__hide:!activeAccountList }">
            ツイッターを登録
        </span>
    </div>
</template>
<script>
import { xl } from '../../../util'
export default {
    props:["activeAccountList","width"],
     methods:{
        twitterRegister(){
            //画面サイズが一定値以下のときはアカウントリストを閉じる。
            if (this.width <= xl && !this.activeAccountList) {
                this.$store.commit('twitter/isList')
            }
            if(!confirm('ツイッターアカウントを登録しますか？')){
                return false
            }
            document.location = `/twitter`
        },
    },
}
</script>