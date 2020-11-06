<template>
<div>
    <i class="far fa-edit c-setting__icon" @click="isChangeForm"></i>
    <h2 class="c-setting__title u-margin__h2--bottom" @click="isChangeForm">フォロワーサーチ</h2>
    <p>プロフィールに含まれているキーワードの条件を設定します。</p>
    <div class="c-setting__switch" @click="isChangeForm">
        <i class="fas fa-toggle-on c-icon--blue"  v-if="isForm"></i>
        <i class="fas fa-toggle-off c-icon--blue"  v-else></i>
    </div>
</div>
</template>
<script>
import { mapState } from 'vuex'

export default {
    methods:{
        //formを表示する。 trueとfalseが入れ替わる.
        isChangeForm(){
            this.$store.commit('autoActionSetting/setIsFollowSearchOpen')
        },
    },
    computed:{
        ...mapState({
            isForm: state => state.autoActionSetting.isFollowSearchOpen,
        })
    }
    
}
</script>