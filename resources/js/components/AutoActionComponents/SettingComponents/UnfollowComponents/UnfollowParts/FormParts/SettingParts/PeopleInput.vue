<template>
    <div class="u-margin__unfollow--bottom">
        <div  class="u-margin__unfollow--bottom">
            1日にフォローを外す人数の上限(0~1000)
        </div>
        <input pattern="\d*" type="number" min="0" max="1000" class="c-number__form" value=1000 v-model="number.people"
        v-on:change="inputPeople">
        <div class="c-error u-margin--error">
            {{ peopleError }}
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
export default {
    data(){
        return{
            number:{
                people:''
            },
        }
    },
    props:["message"],
    methods:{
        //値が入力されたときにUnfollowSettingRegister.vueに渡す。
        inputPeople:function(){
            this.validation()
            this.$emit('people',this.number.people);
        },
        validation(){
            if(!this.number.people){
                if(this.number.people !==0){
                    this.$store.commit('autoActionSetting/setPeopleError',this.message.number)
                    return false
                }
            }
            else if(this.number.people>1000){
                this.$store.commit('autoActionSetting/setPeopleError',this.message.underNumber)
                return false
            }
            else if(!Number.isInteger(Number(this.number.people))){
                this.$store.commit('autoActionSetting/setPeopleError',this.message.integer)
                return false
            }
            else if(Number(this.number.people.indexOf(0)) === 0){
                if(this.number.people >= 1){
                    this.$store.commit('autoActionSetting/setPeopleError',this.message.firstNumber)
                    return false
                }
            }
            this.$store.commit('autoActionSetting/setSettingUnfollowPeople',this.number.people)
            this.$store.commit('autoActionSetting/setPeopleError',"")
        }
    },
    computed:{
        ...mapState({
            settingUnfollowPeople: state => state.autoActionSetting.settingUnfollowPeople,
            peopleError: state => state.autoActionSetting.peopleError,
        }),
    },
    watch:{
        settingUnfollowPeople(){
            this.number.people = this.settingUnfollowPeople
        }
    },
}
</script>