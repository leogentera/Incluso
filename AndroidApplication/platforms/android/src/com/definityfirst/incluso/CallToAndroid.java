package com.definityfirst.incluso;

import android.app.AlertDialog;
import android.app.Dialog;
import android.app.NotificationManager;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.preference.PreferenceManager;
import android.util.Base64;
import android.util.Log;
import android.widget.Toast;

import com.definityfirst.incluso.implementations.DownloadGenericFile;
import com.definityfirst.incluso.implementations.Global;
import com.definityfirst.incluso.implementations.ReadHTML;
import com.definityfirst.incluso.implementations.RestClient;
import com.definityfirst.incluso.implementations.RestClientListener;
import com.definityfirst.incluso.modules.DownloadedFile;
import com.definityfirst.incluso.services.RegistrationIntentService;

import org.apache.cordova.CallbackContext;
import org.apache.cordova.CordovaPlugin;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.List;

public class CallToAndroid extends CordovaPlugin implements RestClientListener {

	final static int FACEBOOK_REGISTRATION=0;
	final static int FACEBOOK_LOGIN=1;
    final static int SELECT_PICTURE=2;
    final static int SUCCESS=-1;
    final static int ERROR=-2;

	public final static String IS_USER_LOGGED="isUserLogged";

    CallbackContext callbackContext;
    String url;
	boolean is_new=false;

	public boolean execute(String action, JSONArray args, CallbackContext callbackContext)
		    throws JSONException {
        Global global=Global.getInstance();
        this.callbackContext=callbackContext;
        global.setCallbackContext(callbackContext);

		SharedPreferences sharedPreferences = PreferenceManager.getDefaultSharedPreferences(global.getMainActivity());

		Log.d("CallToAndroid", "Se ha llamado a una accion: "+ action);
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
                global.setCallbackContextGames(callbackContext);
				Context context=this.cordova.getActivity().getApplicationContext();
				JSONObject jsonObject= new JSONObject(args.getString(0));
				Intent intent=null;
					intent = context.getPackageManager().getLaunchIntentForPackage("com.gentera.inclusointeractivo");

                if (intent == null) {
                    intent = new Intent(Intent.ACTION_VIEW);
                    intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                    intent.setData(Uri.parse("market://details?id=" + "com.gentera.inclusointeractivo"));
                    context.startActivity(intent);
                    JSONObject jsonObject2= new JSONObject();
                    jsonObject2.put("messageerror", Base64.encode("El juego no esta instalado".getBytes(), Base64.DEFAULT));
                    callbackContext.error(jsonObject2);
                    return true;
                }
				intent.setFlags(0);
				//intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
				if (jsonObject.has("avatarType")){
					global.getMainActivity().setAvatarCheckpoint(jsonObject.getString("avatarType"));
					jsonObject.remove("avatarType");
				}

                if (jsonObject.getString("actividad").equals("Mi Avatar") || jsonObject.getString("actividad").equals("Reto múltiple") || jsonObject.getString("actividad").equals("Proyecta tu vida")){
                    jsonObject.put("pathImagen", global.getMainActivity().appFolder+"/"+MainActivity.avatarFolder);
                }

				if (jsonObject.getString("actividad").equals("Tú eliges") || jsonObject.getString("actividad").equals("Multiplica tu dinero") ){
					jsonObject.put("pathImagenes", global.getMainActivity().appFolder+"/"+MainActivity.formsFolder);
				}

				if(jsonObject.getString("actividad").equals("Proyecta tu vida") || jsonObject.get("actividad").equals("Fábrica de emprendimiento")){
					jsonObject.put("imagenFicha", global.getMainActivity().appFolder + "/" + MainActivity.resultsFolder);
					jsonObject.put("pathImagenFicha", global.getMainActivity().appFolder + "/" + MainActivity.resultsFolder);
				}

                intent.putExtra("game_arguments", jsonObject.toString().replace("\\/", "/"));
				//global.setCallbackContext(callbackContext);

				(global.getMainActivity()).startActivity(intent);

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
				intent.putExtra("directory", global.getMainActivity().appPath() +"/" +args.getString(0) );
				intent.putExtra("filename",args.getString(1) );
				intent.putExtra("isLocal",true );
				(global.getMainActivity()).startActivity(intent);
			} catch (Throwable e){
				callbackContext.error("Failed to play video");
			}
			return true;
		}else if (action.equals("PlayWebVideo")){
			try {
				Context context=this.cordova.getActivity().getApplicationContext();

				Intent videoClient = new Intent(Intent.ACTION_VIEW);
				videoClient.setData(Uri.parse( args.getString(0)));
				(global.getMainActivity()).startActivity(videoClient);
			} catch (Throwable e){
				callbackContext.error("Failed to open video");
			}
			return true;
		}
        else if (action.equals("AttachPicture")){
            try {
                Context context=this.cordova.getActivity().getApplicationContext();

                Intent intent = new Intent();
                intent.setType("image/*");
                intent.setAction(Intent.ACTION_GET_CONTENT);
                (global.getMainActivity()).startActivityForResult(Intent.createChooser(intent,
                        "Selecciona una imagen"), SELECT_PICTURE);
            } catch (Throwable e){

                callbackContext.error("Failed to open chooser");
            }
            return true;
        }else if (action.trim().equals("isInstalled")){
            try {
                Context context=this.cordova.getActivity().getApplicationContext();
                Intent intent = context.getPackageManager().getLaunchIntentForPackage("com.gentera.inclusointeractivo");
                JSONObject jsonObject = new JSONObject();
                boolean isInstalled=false;
                try{
                    if (intent != null) {
                        isInstalled=true;
                    }
					jsonObject.put("isInstalled", isInstalled);
                    callbackContext.success(jsonObject);
                }catch(Throwable e){
                    callbackContext.error(new JSONObject().put("messageerror", Base64.encode("No se pudo ejecutar la operacion".getBytes(), Base64.DEFAULT)));
                }



            } catch (Throwable e){
                callbackContext.error(new JSONObject().put("messageerror", Base64.encode("Aplicación no disponible".getBytes(), Base64.DEFAULT)));
            }
            return true;
        }else if (action.trim().equals("setRetoMultipleCallback")){
			try {
                global.setCallbackContextGames(callbackContext);
				global.getMainActivity().onNewIntent(global.getRetosMultiplesIntent());

			} catch (Throwable e){
				callbackContext.error(new JSONObject().put("messageerror", Base64.encode("Aplicación no disponible".getBytes(), Base64.DEFAULT)));
			}
			return true;
		}else if (action.trim().equals("setTuEligesCallback")){
			try {
                global.setCallbackContextGames(callbackContext);
				global.getMainActivity().onNewIntent(global.getTuEligesIntent());
			} catch (Throwable e){
				callbackContext.error(new JSONObject().put("messageerror", Base64.encode("Aplicación no disponible".getBytes(), Base64.DEFAULT)));
			}
			return true;
		}else if (action.trim().equals("setProyectaTuVidaCallback")){
			try {
                global.setCallbackContextGames(callbackContext);
				global.getMainActivity().onNewIntent(global.getProyectaTuVidaIntent());

			} catch (Throwable e){
				callbackContext.error(new JSONObject().put("messageerror", Base64.encode("Aplicación no disponible".getBytes(), Base64.DEFAULT)));
			}
			return true;
		}else if (action.trim().equals("setMultiplicaTuDineroCallback")){
			try {
                global.setCallbackContextGames(callbackContext);
				global.getMainActivity().onNewIntent(global.getMultiplicaTuDineroIntent());

			} catch (Throwable e){
				callbackContext.error(new JSONObject().put("messageerror", Base64.encode("Aplicación no disponible".getBytes(), Base64.DEFAULT)));
			}
			return true;
		}else if (action.trim().equals("setFabricaDeEmprendimientoCallback")) {
			try {
                global.setCallbackContextGames(callbackContext);
				global.getMainActivity().onNewIntent(global.getFabricaDeEmprendimientoIntent());

			} catch (Throwable e) {
				callbackContext.error(new JSONObject().put("messageerror", Base64.encode("Aplicación no disponible".getBytes(), Base64.DEFAULT)));
			}
			return true;
		}else if(action.trim().equals("setMiAvatarIntentCallback")) {
			try {
                global.setCallbackContextGames(callbackContext);
				global.getMainActivity().onNewIntent(global.getMiAvatarIntent());
			}catch (Throwable e) {
				callbackContext.error(new JSONObject().put("messageerror", Base64.encode("Aplicación no disponible".getBytes(), Base64.DEFAULT)));
			}
			return true;
		}else if (action.trim().equals("shareByMail")){
			try {
                List <String> files= new ArrayList<String>();
                files.add(args.getString(0));

                List <String> names= new ArrayList<String>();
                names.add(args.getString(1));
				global.getMainActivity().sendSeveralByMail(files,names, args.getString(2));

			} catch (Throwable e){
				callbackContext.error(new JSONObject().put("messageerror", Base64.encode("No se pudo enviar el mail".getBytes(), Base64.DEFAULT)));
			}
			return true;
		}else if (action.trim().equals("share")){
			try {
				global.getMainActivity().share( JSONArrayToStringList(args));

			} catch (Throwable e){
				callbackContext.error(new JSONObject().put("messageerror", Base64.encode("No se pudo compartir".getBytes(), Base64.DEFAULT)));
			}
			return true;
		}else if (action.trim().equals("download")){
			try {
				global.getMainActivity().download( JSONArrayToStringList(args));

			} catch (Throwable e){
				callbackContext.error(new JSONObject().put("messageerror", Base64.encode("No se pudo guardar".getBytes(), Base64.DEFAULT)));
			}
			return true;
		}else if (action.trim().equals("shareSeveralPicturesByMail")){
            try {
                global.getMainActivity().sendSeveralByMail(JSONArrayToStringList(args.getJSONArray(0)), JSONArrayToStringList(args.getJSONArray(1)), args.getString(2));

            } catch (Throwable e){
                callbackContext.error(new JSONObject().put("messageerror", Base64.encode("No se pudo enviar el mail".getBytes(), Base64.DEFAULT)));
            }
            return true;
        }else if (action.trim().equals("datepicker")){
			global.getMainActivity().openDatePickerDialog(args.getString(0));
			return true;
		}
		else if (action.trim().equals("restart")){
			global.getMainActivity().restart();
			return true;
		}else if (action.trim().equalsIgnoreCase("getVersion")){
			global.setCallbackContextVersion(callbackContext);
			ReadHTML readHTML=new ReadHTML(global.getMainActivity(), global.getMainActivity(),  "");
			readHTML.execute(global.getMainActivity().appVersionGetter);
			return true;
		}else if (action.trim().equalsIgnoreCase("isOnline")){
			JSONObject jsonObject=new JSONObject();
			jsonObject.put("online", global.getMainActivity().isOnline());
			callbackContext.success(jsonObject);

			return true;
		}else if (action.trim().equalsIgnoreCase("downloadPictures")){
			/*ArrayList<DownloadedFile> files=  new Gson().fromJson(args.getString(0),
					List<DownloadedFile>.getClass());*/
			List <DownloadedFile> files=  new ArrayList<DownloadedFile>();

			JSONArray jsonArray = new JSONArray(args.getString(0));

			for (int i=0; i<jsonArray.length();i++){
				files.add(new DownloadedFile((JSONObject) jsonArray.get(i)));
			}

			DownloadGenericFile dgf=new DownloadGenericFile(global.getMainActivity(), callbackContext,files , global.getMainActivity().appPath());
			dgf.execute("");

			return true;
		}else if (action.trim().equalsIgnoreCase("fileExists")){
			String filepath = args.getString(0);

			String file = global.getMainActivity().appPath() + "/"+filepath;

            File tmp= new File(file);

            boolean exists= tmp.exists();

            JSONObject jsonObject= new JSONObject();
            jsonObject.put("exists", exists);

            callbackContext.success(jsonObject);

			return true;
		}
		else if (action.trim().equalsIgnoreCase("login")){
			sharedPreferences.edit().putBoolean(IS_USER_LOGGED, true).apply();
			JSONObject jsonObjectWebApp = new JSONObject(args.getString(0));
			try{
				if (global.getMainActivity().checkPlayServices()){
					Intent intent = new Intent(global.getMainActivity(), RegistrationIntentService.class);
					intent.putExtra("url", (String)jsonObjectWebApp.get("moodleAPI"));
					intent.putExtra("moodleToken", (String)jsonObjectWebApp.get("moodleToken") );
					global.getMainActivity().startService(intent);
				}
			}catch(Throwable e){

			}

			return true;
		}
		else if (action.trim().equalsIgnoreCase("logout")){
			sharedPreferences.edit().putBoolean(IS_USER_LOGGED, false).apply();
            JSONObject jsonObjectWebApp = new JSONObject(args.getString(0));
            JSONObject jsonObject = new JSONObject();

            try {
                jsonObject.put("token", global.getSharedPreferences().getString(RegistrationIntentService.TOKEN, ""));
                jsonObject.put("register", false);

                RestClient restClient= new RestClient(global.getMainActivity(), this, RestClient.POST, "application/json", jsonObject.toString(), RegistrationIntentService.SEND_GCM_UNREGISTRATION );
                restClient.addHeader("Authorization", (String)jsonObjectWebApp.get("moodleToken"));
                restClient.execute((String)jsonObjectWebApp.get("moodleAPI")+"gcm");
            } catch (JSONException e) {
                e.printStackTrace();
            }

			return true;
		}//TODO
		else if (action.trim().equalsIgnoreCase("seenNotification")){

            NotificationManager mNotificationManager =
                    (NotificationManager) global.getMainActivity().getSystemService(Context.NOTIFICATION_SERVICE);

            mNotificationManager.cancel(Integer.parseInt(args.getString(0)));
			return true;
		}
        else if (action.trim().equalsIgnoreCase("getImage")) {
            try {
                callbackContext.success(getImageBase64(args.getString(0)));

            } catch (Throwable e) {
                callbackContext.error(new JSONObject().put("messageerror", Base64.encode("Error al postear la imagen".getBytes(), Base64.DEFAULT)));
            }
            return true;
        }
		else if (action.trim().equalsIgnoreCase("isCellphone")) {
				callbackContext.success();

			return true;
		}
        else if (action.trim().equalsIgnoreCase("showCombobox")) {
            final String field= args.getString(0);
            args.remove(0);
            final String[] options = JSONArrayToStringArray(args);

            AlertDialog.Builder builder = new AlertDialog.Builder(global.getMainActivity());
            builder.setItems(options, new DialogInterface.OnClickListener() {
                        public void onClick(DialogInterface dialog, int which) {
                            JSONObject object = new JSONObject();
                            try {
                                object.put("field", field);
                                object.put("which", which+1);
                                CallToAndroid.this.callbackContext.success(object);
                            } catch (JSONException e) {
                                e.printStackTrace();
                            }


                        }
                    });
            Dialog dialog= builder.create();
            dialog.show();
            return true;
        }
		else if (action.trim().equalsIgnoreCase("reloadPage")) {
			final String field= args.getString(0);
			final File file = new File(global.getMainActivity().appFolder, "redirectToAndroid.html");
			Uri uri = Uri.fromFile(file);
			global.getMainActivity().loadUrl(uri.toString() + "?url=index.html#/" + field);
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

						if (finalJsonObj.has("is_new")){
							is_new= finalJsonObj.getBoolean("is_new");
						}

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
						finalJsonObj.put("is_new", is_new);
						callbackContext.success(finalJsonObj.toString());
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

	public List<String> JSONArrayToStringList(JSONArray jsonArray){
		List<String> list = new ArrayList<String>();

		for(int i = 0; i < jsonArray.length(); i++){
			try {
				list.add(jsonArray.getString(i));
			} catch (JSONException e) {
				e.printStackTrace();
			}
		}

		return list;
	}

    public String[] JSONArrayToStringArray(JSONArray jsonArray){
       String[]list=new String[jsonArray.length()];

        for(int i = 0; i < jsonArray.length(); i++){
            try {
                list[i]=jsonArray.getString(i);
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }

        return list;
    }

	public String getImageBase64(String path) throws Throwable {
		File fimage= new File(path);
		FileInputStream fis= new FileInputStream(fimage);
		//FileInputStream fis= new FileInputStream(fimage);

		byte[] image= new byte[fis.available()];
		fis.read(image);

		return new String(Base64.encode(image, Base64.DEFAULT));
	}
}

