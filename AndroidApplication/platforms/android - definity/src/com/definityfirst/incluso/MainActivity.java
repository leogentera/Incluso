/*
       Licensed to the Apache Software Foundation (ASF) under one
       or more contributor license agreements.  See the NOTICE file
       distributed with this work for additional information
       regarding copyright ownership.  The ASF licenses this file
       to you under the Apache License, Version 2.0 (the
       "License"); you may not use this file except in compliance
       with the License.  You may obtain a copy of the License at

         http://www.apache.org/licenses/LICENSE-2.0

       Unless required by applicable law or agreed to in writing,
       software distributed under the License is distributed on an
       "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
       KIND, either express or implied.  See the License for the
       specific language governing permissions and limitations
       under the License.
 */

package com.definityfirst.incluso;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.util.Arrays;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.util.Base64;
import android.widget.Toast;

import  com.definityfirst.incluso.implementations.Global;
import  com.definityfirst.incluso.modules.DownloadFileFromPHP;
import  com.definityfirst.incluso.modules.DownloadFileFromPackage;
import  com.definityfirst.incluso.modules.DownloadFileListener;
import com.definityfirst.incluso.modules.RestClient;
import com.definityfirst.incluso.modules.RestClientListener;
import  com.definityfirst.incluso.ui.SpinnerDialog;

import com.facebook.AccessToken;
import com.facebook.CallbackManager;
import com.facebook.FacebookCallback;
import com.facebook.FacebookException;
import com.facebook.FacebookSdk;
import com.facebook.GraphRequest;
import com.facebook.GraphResponse;
import com.facebook.Profile;
import com.facebook.ProfileTracker;
import com.facebook.appevents.AppEventsLogger;
import com.facebook.login.LoginManager;
import com.facebook.login.LoginResult;
import com.facebook.login.widget.LoginButton;

import org.apache.cordova.*;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;


public class MainActivity extends CordovaActivity implements DownloadFileListener
{

    final static int DUMMY_GAME=0;
	Handler handler;
    SpinnerDialog sp_dialog;

    String appFolder =Environment.getExternalStorageDirectory()+"/app/initializr";
    int rawFolder=R.raw.app;

    Global global;

    boolean deleteFiles=false;

    String appWebResource="http://incluso.definityfirst.com/android/package/content.php";
    //String appWebResource="";
    LoginButton loginButton;

    CallbackManager callbackManager;
    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        FacebookSdk.sdkInitialize(getApplicationContext());

        //initializeLoginButton();
        // Set by <content src="index.html" /> in config.xml
        global=Global.getInstance();
        global.setMainActivity(this);
        handler=new Handler();
        sp_dialog= new SpinnerDialog(this);
        //super.setIntegerProperty("splashscreen", android.R.color.black);
        // Background of activity:
        //appView.getView().setBackgroundColor(getResources().getColor(android.R.color.black));
        installApp();
        //this.startActivity(new Intent(this, video_player.class));
    }

    @SuppressLint("NewApi")
    public void loginWithFacebook(final RestClientListener listener, final String url){
        //loginButton= new LoginButton(this);
        //loginButton.setReadPermissions("user_friends", "user_birthday", "user_location", "email");
        callbackManager = CallbackManager.Factory.create();

        Profile profile=Profile.getCurrentProfile();

        LoginManager.getInstance().registerCallback(callbackManager, new FacebookCallback<LoginResult>() {
            @Override
            public void onSuccess(LoginResult loginResult) {
                // App code
                if (Profile.getCurrentProfile()==null){
                    final ProfileTracker mProfileTracker = new ProfileTracker() {
                        @Override
                        protected void onCurrentProfileChanged(Profile profile, Profile profile2) {
                            getFacebookData(AccessToken.getCurrentAccessToken(), listener, url);
                            stopTracking();
                        }
                    };
                    mProfileTracker.startTracking();
                }
                else{
                    getFacebookData(AccessToken.getCurrentAccessToken(), listener, url);
                }


            }

            @Override
            public void onCancel() {
                // App code
                listener.finishPost("{\"messageerror\":\""+Base64.encodeToString("Se ha cancelado el login".getBytes(), Base64.NO_WRAP)+"\"}", SayHelloPlugin.ERROR);
            }

            @Override
            public void onError(FacebookException exception) {
                // App code
                listener.finishPost("{\"messageerror\":\""+Base64.encodeToString("Ocurrio un error, contacte al administrador".getBytes(), Base64.DEFAULT)+"\"}", SayHelloPlugin.ERROR);
            //listener.finishPost(, SayHelloPlugin.ERROR);
            }
        });

        /*if (profile==null){
            loginButton.callOnClick();
            profile=Profile.getCurrentProfile();
        }*/

        LoginManager.getInstance().logInWithReadPermissions(this, Arrays.asList("public_profile", "user_friends", "user_birthday", "user_location", "email"));
        //if (profile!=null){


       // }

       // LoginManager.getInstance().logOut();
    }

    public void getFacebookData(AccessToken accessToken, final RestClientListener listener, final String url){
        final Profile finalProfile = Profile.getCurrentProfile();
        GraphRequest request = GraphRequest.newMeRequest(
                accessToken,
                new GraphRequest.GraphJSONObjectCallback() {
                    @Override
                    public void onCompleted(
                            JSONObject object,
                            GraphResponse response) {
                        // Application code
                        //Toast.makeText(MainActivity.this, "", Toast.LENGTH_LONG);
                        try {

                            String birthday = "";
                            String gender = getGender("Male");
                            String firstName = "";
                            String lastName = "";
                            String motherName = "";

                            if (object.has("birthday")) {
                                birthday = object.getString("birthday");
                            }

                            if (object.has("gender")) {
                                gender = getGender(object.getString("gender"));
                            }

                            if (object.has("first_name")) {
                                firstName = object.getString("first_name");
                            }

                            if (object.has("last_name")) {
                                lastName = object.getString("last_name");
                            }

                            String post = "username=&password=Facebook123!&email=" +
                                    object.getString("email") +
                                    "&city=" +
                                    "&country=" +
                                    "&secretanswer=" + //facebook4113" +
                                    "&secretquestion=" + //Nombre de mi mejor amigo" +
                                    "&birthday=" + birthday +
                                    "&gender=" + gender +
                                    "&alias=" + object.getString("name") +
                                    "&facebookid=" + finalProfile.getId() +
                                    "&firstname=" + firstName +
                                    "&lastname=" + lastName +
                                    "&mothername=";

                            RestClient restClient = new RestClient(MainActivity.this, listener, RestClient.POST, "application/x-www-form-urlencoded", post, SayHelloPlugin.FACEBOOK_REGISTRATION);
                            restClient.execute(url + "/user");
                            LoginManager.getInstance().logOut();
                        } catch (JSONException e) {
                            LoginManager.getInstance().logOut();
                            try {
                                global.getCallbackContext().error(new JSONObject().put("messageerror", "Error al registrarse"));
                            } catch (JSONException e1) {
                                e1.printStackTrace();
                            }
                            e.printStackTrace();
                        }

                        // LoginManager.getInstance().logOut();
                    }
                });
        Bundle parameters = new Bundle();
        parameters.putString("fields", "id,name,  email, link,birthday,location, gender, first_name, last_name");
        request.setParameters(parameters);
        request.executeAsync();
    }

    private String getGender(String gender) {
        if (gender.equalsIgnoreCase("male")){
            return "Masculino";
        }
        else{
            return "Femenino";
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
//        installApp();
        AppEventsLogger.activateApp(this);

    }

    @Override
	public void loadFinish() {
		// TODO Auto-generated method stub
		final File file = new File(appFolder, "index.html");
        Uri uri = Uri.fromFile(file);
        loadFinish(uri.toString());

	}

	@Override
	public void loadFinish(final String page) {
		// TODO Auto-generated method stub
		handler.post(new Runnable() {

            @Override
            public void run() {
                // TODO Auto-generated method stub
                sp_dialog.hideDialog();
                loadUrl(page);

                //startNewActivity(MainActivity.this, "com.sieena.pdi2");
            }

        });
		
		
	}

    @Override
    public void localLoadFinish(String page) {
        sp_dialog.hideDialog();
        if (isOnline()){
            checkForNewVersion();
        }
        else
        {
            loadFinish(page);

        }

    }

    @Override
    public void changeSpinnerText(final String text) {
        handler.post(new Runnable() {

            @Override
            public void run() {
                sp_dialog.setText(text);
            }
        });

    }

    public String getVersion(){
        return getVersion(appFolder);
    }

    public String getVersion(String path){
        final File file = new File(path, "version.txt");
        String version="";
        try {
            BufferedReader reader= new BufferedReader(new FileReader(file));
            long bufferedLength=0;
            while (bufferedLength<file.length()){
                version+=Character.toString((char) reader.read());
                bufferedLength++;
            }
        } catch (FileNotFoundException e) {
            e.printStackTrace();
        } catch (IOException e) {
            e.printStackTrace();
        }

        if (version.trim().equals("")){
            version="0";
        }

        if (version.equals("1.0.30")){
            deleteFiles=true;
            return "0.0.0";
        }
        return version;
    }


    /*
 * isOnline - Check if there is a NetworkConnection
 * @return boolean
 */
    protected boolean isOnline() {
        ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo netInfo = cm.getActiveNetworkInfo();
        return netInfo != null && netInfo.isConnected();
    }

    public void installApp(){

        if (  getVersion().equals("0")) {
            DownloadFileFromPackage dfp=new DownloadFileFromPackage(this, this, appFolder, rawFolder);
            sp_dialog.showDialog("Instalando version de la aplicacion");
            dfp.execute(appWebResource);
        }
        else if (isOnline()){

            checkForNewVersion();
        }
        else
        {
            loadFinish();

        }
    }

    public void checkForNewVersion(){
        DownloadFileFromPHP df=new DownloadFileFromPHP(this, this, appFolder);
        sp_dialog.showDialog("Obteniendo ultima version de la aplicacion");
        String version=getVersion();
        if (deleteFiles){
            deleteAll();
            deleteFiles=false;
        }
        df.execute(appWebResource + "?version="+version);
    }

    public void startNewActivity(Context context, String packageName) {
        Intent intent = context.getPackageManager().getLaunchIntentForPackage(packageName);
        if (intent != null) {
            // We found the activity now start the activity
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            context.startActivity(intent);
        } else {
            // Bring user to the market or let them choose an app?
            intent = new Intent(Intent.ACTION_VIEW);
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            intent.setData(Uri.parse("market://details?id=" + packageName));
            context.startActivity(intent);
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent intent) {
        //super.onActivityResult(requestCode, resultCode, intent);

        switch (requestCode){
            case DUMMY_GAME:
                if (resultCode==RESULT_OK){
                   Bundle bu_params= intent.getExtras();
                   /* AlertDialog.Builder builder = new AlertDialog.Builder(this);
                    builder.setMessage()
                            .setPositiveButton("OK", new DialogInterface.OnClickListener() {
                                public void onClick(DialogInterface dialog, int id) {
                                    // FIRE ZE MISSILES!
                                }
                            });
                    // Create the AlertDialog object and return it
                   builder.create().show();*/

                    String message=bu_params.getString("parametros_juego");
                    /*new AlertDialog.Builder(this)
                            .setTitle("Delete entry")
                            .setMessage(message)
                            .setPositiveButton(android.R.string.yes, new DialogInterface.OnClickListener() {
                                public void onClick(DialogInterface dialog, int which) {
                                    // continue with delete
                                }
                            })
                            .show();*/

                    CallbackContext callbackContext = global.getCallbackContext();
                    if (callbackContext!=null)
                        callbackContext.success();
                    else{
                        Toast.makeText(MainActivity.this, "Ocurrio un error, contacte al administrador", Toast.LENGTH_SHORT).show();
                    }
                }
                else{
                    CallbackContext callbackContext = global.getCallbackContext();
                    if (callbackContext!=null)
                        try {
                            callbackContext.error(new JSONObject().put("messageerror", "Cancelada por el Usuario"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    else{
                        Toast.makeText(MainActivity.this, "Ocurrio un error, contacte al administrador", Toast.LENGTH_SHORT).show();
                    }
                }
                break;
        }

        if (callbackManager!=null)
            callbackManager.onActivityResult(requestCode, resultCode, intent);
    }
    @Override
    protected void onPause() {
        super.onPause();

        // Logs 'app deactivate' App Event.
        AppEventsLogger.deactivateApp(this);
    }

    public String capitalize(String string){
        char[] array = string.toCharArray();

        array[0] = Character.toUpperCase(array[0]);

        return new String(array);
    }
    public void deleteAll(){
        File dir = new File(appFolder);
        if (dir.isDirectory())
        {
            String[] children = dir.list();
            for (int i = 0; i < children.length; i++)
            {
                new File(dir, children[i]).delete();
            }
        }
    }

    @Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        global.getCallbackContext().success(intent.getExtras().getString("game_arguments"));
    }


}
