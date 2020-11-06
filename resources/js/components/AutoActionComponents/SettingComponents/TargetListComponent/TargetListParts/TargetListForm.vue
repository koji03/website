<template>
    <transition name="p-fade">
        <div v-show="isTargetListOpen">
            <Loading v-if="targetListFormLoad" />
            <Form/>
            <AccountList/>
            <note/>
        </div>
    </transition>
</template>
<script>
import Loading from '../../../../Loading.vue'
import note from './FormParts/TargetListNote'
import Form from './FormParts/RegisterIdForm'
import AccountList from './FormParts/TargetAccountList'
import { mapState } from 'vuex'
export default {
    data(){
        return{
            targetList:[],
        }
    },
    components:{
        Loading,
        Form,
        AccountList,
        note
    },
    computed:{
        ...mapState({
            isTargetListOpen: state => state.autoActionSetting.isTargetListOpen,
            targetListFormLoad: state => state.autoActionSetting.targetListFormLoad,
        })
    }

}
</script>