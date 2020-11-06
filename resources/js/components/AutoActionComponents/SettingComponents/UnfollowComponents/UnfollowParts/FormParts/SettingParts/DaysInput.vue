<template>
    <div class="u-margin__unfollow--bottom">
        <div class="u-margin__unfollow--bottom">
            フォローから経過した日数(7~1000)
        </div>
        <input　pattern="\d*" type="number" min="7" max="1000" class="c-number__form" value=7 v-model="number.days"
        v-on:change="inputDays"
        >
        <div class="c-error u-margin--error">
            {{ daysError }}
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
export default {
    data(){
        return{
            number:{
                days:'',
            },
        }
    },
    props:["message"],
    methods:{
        //値が入力されたときにUnfollowSettingRegister.vueに渡す。
        inputDays:function(){
            this.validation()
            this.$emit('days',this.number.days);
        },
        validation(){
            if(!this.number.days){
                if(this.number.days !==0){
                    this.$store.commit('autoActionSetting/setDaysError',this.message.number)
                    return false
                }
            }
            else if(this.number.days>1000){
                this.$store.commit('autoActionSetting/setDaysError',this.message.underNumber)
                return false
            }
            else if(this.number.days<7){
                this.$store.commit('autoActionSetting/setDaysError',this.message.moreNumber)
                return false
            }
            else if(!Number.isInteger(Number(this.number.days))){
                this.$store.commit('autoActionSetting/setDaysError',this.message.integer)
                return false
            }
            else if(Number(this.number.days.indexOf(0)) === 0){
                this.$store.commit('autoActionSetting/setDaysError',this.message.firstNumber)
                return false
            }
            this.$store.commit('autoActionSetting/setSettingUnfollowDays',this.number.days)
            this.$store.commit('autoActionSetting/setDaysError',"")
        }
    },
    computed:{
        ...mapState({
            settingUnfollowDays: state => state.autoActionSetting.settingUnfollowDays,
            daysError: state => state.autoActionSetting.daysError,
        }),
    },
    watch:{
        settingUnfollowDays(){
            this.number.days = this.settingUnfollowDays
        }
    }
}
</script>