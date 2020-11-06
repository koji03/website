<template>
    <div>
        <i class="fas fa-user-minus c-setting__icon" @click="isChangeForm"></i>
        <h2 class="c-setting__title u-margin__h2--bottom" @click="isChangeForm">アンフォロー</h2>
        <p>フォローを外す条件を設定します。
            <br>
        </p>
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
        //formを表示する。
        isChangeForm(){
            this.$store.commit('autoActionSetting/setUnfollowOpen')
        },
    },
    computed:{
        ...mapState({
            isForm: state => state.autoActionSetting.isUnfollowOpen,
        }),
    }
    
}
</script>