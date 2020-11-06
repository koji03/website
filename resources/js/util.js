/**
CSRF対策
CSRFトークンはレスポンスのたびにクッキーに入れて送信されているので
クッキーからトークンを取り出してHTTPヘッダーにそのトークンを含めてリクエストを送信してCSRFチェックをする。

document.cookieによってクッキーを以下の形式で参照できる
name=12345;token=67890;key=abcde
 */
export function getCookieValue (searchKey) {
    if (typeof searchKey === 'undefined') {
      return ''
    }
  
    let val = ''
  
    document.cookie.split(';').forEach(cookie => {
      const [key, value] = cookie.split('=')
      if (key === searchKey) {
        return val = value
      }
    })
  
    return val
  }

  //エラーハンドリング
  export const OK = 200
  export const CREATED = 201
  export const INTERNAL_SERVER_ERROR = 500
  export const UNPROCESSABLE_ENTITY = 422
  export const UNAUTHORIZED = 419
  export const NOT_FOUND = 404
  export const Key_Account = 'Not authorized.'
  export const User_has_been_suspended = 63;
  export const Rate_limit_exceeded = 88;
  export const account_is_temporarily_locked = 326;
  export const You_have_already_favorited = 139;
  export const User_not_found = 50;

  export const sm = 500;
  export const md = 768;
  export const lg = 1000;
  export const xl = 1200;