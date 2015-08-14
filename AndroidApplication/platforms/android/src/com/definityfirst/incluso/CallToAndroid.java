package com.definityfirst.incluso;

import android.content.Context;
import android.content.Intent;
import android.os.Environment;
import android.util.Base64;
import android.widget.Toast;

import com.definityfirst.incluso.implementations.Global;
import com.definityfirst.incluso.modules.RestClient;
import com.definityfirst.incluso.modules.RestClientListener;
import com.facebook.login.LoginManager;

import org.apache.cordova.CallbackContext;
import org.apache.cordova.CordovaPlugin;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class CallToAndroid extends CordovaPlugin implements RestClientListener {

	final static int FACEBOOK_REGISTRATION=0;
	final static int FACEBOOK_LOGIN=1;
    final static int SUCCESS=-1;
    final static int ERROR=-2;

    CallbackContext callbackContext;
    String url;

	public boolean execute(String action, JSONArray args, CallbackContext callbackContext)
		    throws JSONException {
        Global global=Global.getInstance();
        this.callbackContext=callbackContext;
		if (action.equals("sayHello")){
	        try {
	        	Context context=this.cordova.getActivity().getApplicationContext(); 
	        	Toast.makeText(context, args.getString(0), Toast.LENGTH_SHORT).show();
	            String responseText = "Hello " + args.getString(0);
	            callbackContext.success(responseText);
	        } catch (JSONException e){
	            callbackContext.error("Failed to parse parameters");
	        }
	        return true;
	    }
		else if (action.equals("openApp")){
			try {
				Context context=this.cordova.getActivity().getApplicationContext();
				Intent intent = context.getPackageManager().getLaunchIntentForPackage("com.definityfirst.humbertocastaneda.dummygame");
                intent.setFlags(0);
				//intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
				intent.putExtra("parametros_juego", args.getString(0));
				global.setCallbackContext(callbackContext);
                (global.getMainActivity()).startActivityForResult(intent, MainActivity.DUMMY_GAME);
			} catch (Throwable e){
				callbackContext.error(new JSONObject().put("messageerror", "Aplicación no disponible"));
			}
			return true;
		}
		else if (action.equals("connectWithFacebook")){
			try {
				Context context=this.cordova.getActivity().getApplicationContext();
                url= args.getString(0);
                global.getMainActivity().loginWithFacebook(this, url);
			} catch (Throwable e){
				callbackContext.error("Failed to parse parameters");
			}
			return true;
		}

		else if (action.equals("PlayLocalVideo")){
			try {
				Context context=this.cordova.getActivity().getApplicationContext();

				Intent intent=new Intent(context, VideoPlayer.class);
				intent.putExtra("directory", Environment.getExternalStorageDirectory() + "/app/initializr/" +args.getString(0) );
				intent.putExtra("filename",args.getString(1) );
				intent.putExtra("isLocal",true );
				(global.getMainActivity()).startActivity(intent);
			} catch (Throwable e){
				callbackContext.error("Failed to play video");
			}
			return true;
		}
		return false;
	}

	@Override
	public void finishPost(final String result, int RequestCode) {
		final Global global=Global.getInstance();
		try {
			JSONObject jsonObj=null;
			if (result.startsWith("{"))
				jsonObj= new JSONObject(result);

			if (RequestCode==FACEBOOK_REGISTRATION){
				final JSONObject finalJsonObj = jsonObj;


				if (finalJsonObj!=null){
					if (finalJsonObj.has("messageerror")){
						//Toast.makeText(global.getMainActivity(), new String(Base64.decode(finalJsonObj.getString("messageerror"), Base64.DEFAULT)), Toast.LENGTH_LONG).show();
						callbackContext.error(result);

						//LoginManager.getInstance().logOut();
					}
					else{
						//Everything ok
						String post= "username="+finalJsonObj.getString("username")+"&password=Facebook123!";

						RestClient restClient= new RestClient(global.getMainActivity(), CallToAndroid.this, RestClient.POST, "application/x-www-form-urlencoded", post, FACEBOOK_LOGIN );
						restClient.execute(url+"/authentication");
					}

				}
				else{
					callbackContext.error("{\"messageerror\":\""+ Base64.encodeToString("Ocurrio un error, no se puso registrar al sistema".getBytes(), Base64.DEFAULT)+"\"}");
				}





			}
			else if (RequestCode==FACEBOOK_LOGIN){
				final JSONObject finalJsonObj = jsonObj;

				if (finalJsonObj!=null){
					if (finalJsonObj.has("messageerror")){
						//Toast.makeText(global.getMainActivity(), new String(Base64.decode(finalJsonObj.getString("messageerror"), Base64.DEFAULT)), Toast.LENGTH_LONG).show();
						callbackContext.error(result);
						//LoginManager.getInstance().logOut();
					}
					else{
						//Everything ok
						//Toast.makeText(global.getMainActivity(), result, Toast.LENGTH_LONG).show();
						callbackContext.success(result);
						//LoginManager.getInstance().logOut();
					}

				}
				else{
					callbackContext.error("{\"messageerror\":\""+ Base64.encodeToString("Ocurrio un error, error al momento de ingresar".getBytes(), Base64.DEFAULT)+"\"}");
				}




			}
			else if (RequestCode==SUCCESS){
				callbackContext.success(result);
			}
			else if (RequestCode==ERROR){
				callbackContext.error(result);
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}




	}
}

