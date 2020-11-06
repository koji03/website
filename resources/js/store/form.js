const state ={
    errorMessage:{
        email:{
            email:"メールアドレスに誤りがあります。ご確認いただき、正しく変更してください。"
        },
        password:{
            length:"8文字以上32文字以下の半角英数字を指定してください。",
            alphanumeric:"英字と数字両方を含むパスワードを設定してください。",
            confirmation:"パスワードと一致しません。",
        },
        name:{
            length:"255文字以内にしてください。"
        },        
    },
    message:{
        success:{
            reset:'パスワードの変更に成功しました。',
            register:'ユーザー登録に成功しました。'
        },
        placeholder:{
            password:"8文字以上の半角英数字",
            name:"例）かみっ太郎",
            email:"例）kami@kamitter",
        },
        title:{
            reset:"パスワードの変更",
            main:"kamiTTer"
        },
        text:{
            reset:"新しいパスワードを入力してパスワードを変更するを押してください。",
            register:"作成したアカウントにお持ちのツイッターアカウントを登録して運用します"
        }
            ,
        label:{
            password:"パスワード(必須)",
            confirmation:"パスワード（再入力）(必須)",
            name:"ニックネーム(必須)",
            email:"メールアドレス(必須)",
            password:"パスワード(必須)",
            confirmation:"パスワード（再入力）(必須)"
        },
        submit:{
            login:"ログイン",
            reminder:"送信",
            register:"アカウントを作成する",
            reset:"パスワードを変更する"
        },
        routerLink:{
            reset:"パスワードを忘れた",
            register:"アカウントを作成"
        }
    }
}
const mutations = {
}

const actions = {
}

const getters = {
    message: state => state.message
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}