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
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.List;
import java.util.Timer;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.provider.MediaStore;
import android.provider.OpenableColumns;
import android.support.v4.app.NotificationCompat;
import android.util.Base64;
import android.util.Log;
import android.widget.DatePicker;
import android.widget.Toast;

import  com.definityfirst.incluso.implementations.Global;
import com.definityfirst.incluso.implementations.DownloadFileFromPHP;
import com.definityfirst.incluso.implementations.DownloadFileFromPackage;
import com.definityfirst.incluso.implementations.DownloadFileListener;
import com.definityfirst.incluso.implementations.RestClient;
import com.definityfirst.incluso.implementations.RestClientListener;
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
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GoogleApiAvailability;

import org.apache.cordova.*;
import org.json.JSONException;
import org.json.JSONObject;


public class MainActivity extends CordovaActivity implements DownloadFileListener, DatePickerDialog.OnDateSetListener
{

    final static int DOWNLOADING_NOTIFICATION=0;
    final static int DOWNLOAD_NOTIFICATION=1;
    final static int DUMMY_GAME=0;
    private static final int PLAY_SERVICES_RESOLUTION_REQUEST = 9000;
	Handler handler;
    SpinnerDialog sp_dialog;
    boolean preventToLoad =false;

    String appFolder="";
    public final static String appRootFolder="/app/initializr";
    final static String avatarFolder="assets/avatar";
    final static String formsFolder="assets/images/forms";
    final static String resultsFolder = "assets/images/results";
    int rawFolder=R.raw.app;

    Global global;

    boolean deleteFiles=false;

    //String server="http://incluso.definityfirst.com/android/package";
    //String server="http://10.15.1.255/publisher";
    String server="http://inclws03.cloudapp.net";
    String appWebResource=server+"/content.php";
    String appVersionGetter=server+"/version.php";
    String moodleAPI="http://definityincluso.cloudapp.net:82/restfulapiv2-5/RestfulAPI/public";
    String moodleToken="b6c6784dcd49360be56b450bab4166ed";

    final static public String NOTIFICATION_INTENT="notificationIntent";
    final static public String POST_ID="postid";

    //String appWebResource="";
    //String appWebResource="http://inclws03.cloudapp.net/content.php";

    LoginButton loginButton;

    CallbackManager callbackManager;

    public final  String appPath(){
        File folder=getExternalFilesDir(null);
        return folder.getAbsolutePath()+appRootFolder;
    }

    public final  String appPathAbsolute(){
        File folder=getExternalFilesDir(null);
        return folder.getAbsolutePath();
    }
    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        appFolder=appPath();
        deleteOldFiles();
        FacebookSdk.sdkInitialize(getApplicationContext());

        //initializeLoginButton();
        // Set by <content src="index.html" /> in config.xml
        global=Global.getInstance();

        global.setMainActivity(this);
        handler=new Handler();
        sp_dialog= new SpinnerDialog(this);

      /*  if (checkPlayServices()){
            Intent intent = new Intent(this, RegistrationIntentService.class);
            intent.putExtra("url", moodleAPI);
            intent.putExtra("moodleToken", moodleToken);
            startService(intent);
        }*/

        installApp();

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
                listener.finishPost("{\"messageerror\":\""+Base64.encodeToString("Se ha cancelado el login".getBytes(), Base64.NO_WRAP)+"\"}", CallToAndroid.ERROR);
            }

            @Override
            public void onError(FacebookException exception) {
                // App code
                listener.finishPost("{\"messageerror\":\""+Base64.encodeToString("5000 - Ocurrio un error".getBytes(), Base64.DEFAULT)+"\"}", CallToAndroid.ERROR);
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

                            RestClient restClient = new RestClient(MainActivity.this, listener, RestClient.POST, "application/x-www-form-urlencoded", post, CallToAndroid.FACEBOOK_REGISTRATION);
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
        System.gc();
        AppEventsLogger.activateApp(this);
        global.setmIsInForegroundMode(true);
    }

    @Override
	public void loadFinish() {
		// TODO Auto-generated method stub
        //final File file = new File(appFolder, "index.html");
        final File file = new File(appFolder, "redirectToAndroid.html");
        Uri uri = Uri.fromFile(file);
        if (gamesReturn (getIntent()))
            loadFinish(uri.toString() + "?url=index.html&imacellphone=true");


    }

    @Override
    public void loadFinish(final String page) {
        // TODO Auto-generated method stub
        handler.post(new Runnable() {

            @Override
            public void run() {
                // TODO Auto-generated method stub
                sp_dialog.hideDialog();
                if (!preventToLoad) {
                    loadUrl(page);
                    Timer timer = new Timer();

                    //loadUrl("javascript:imacelphone()");
                    if (gamesReturn (getIntent()))
                        loadUrl("javascript:var _isCellPhone=false ;function a (){ _isCellPhone=true;} a();");

                }
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

    @Override
    public void finishGotVersion(String version) {
        String currentVersion=getVersion();
        String latestVersion=version;

        JSONObject versions= new JSONObject();

        try {
            versions.put("currentVersion", currentVersion);
            versions.put("latestVersion", version);
            versions.put("apkVersion", BuildConfig.VERSION_CODE);
        } catch (JSONException e) {
            e.printStackTrace();
        }

        if ( global.getCallbackContextVersion()!=null){
            global.getCallbackContextVersion().success(versions);
        }
        else{
            Toast.makeText(MainActivity.this, "1000 - Ocurrio un error", Toast.LENGTH_SHORT).show();
        }

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
            version="0.0.0";
        }

       /* if (version.equals("1.0.30")){
            deleteFiles=true;
            return "0.0.0";
        }*/
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

        if (  getVersion().equals("0") ||  getVersion().equals("0.0.0")) {
            DownloadFileFromPackage dfp=new DownloadFileFromPackage(this, this, appFolder, rawFolder);
            sp_dialog.showDialog("Instalando la aplicación");
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
        sp_dialog.showDialog("Comparando la última versión de la aplicación");
        String version=getVersion();
        /*if (deleteFiles){
            //deleteAll();
            deleteFiles=false;
        }*/
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

                    String message=bu_params.getString("parametros_juego");

                    CallbackContext callbackContext = global.getCallbackContext();
                    if (callbackContext!=null)
                        callbackContext.success();
                    else{
                        Toast.makeText(MainActivity.this, "2001 - Ocurrio un error", Toast.LENGTH_SHORT).show();
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
                        Toast.makeText(MainActivity.this, "2002 - Ocurrio un error", Toast.LENGTH_SHORT).show();
                    }
                }
                break;
            case CallToAndroid.SELECT_PICTURE:
                if (resultCode == RESULT_OK) {

                        Uri selectedImageUri = intent.getData();
                        JSONObject jsonObject = new JSONObject();
                    try {
                        String scheme = selectedImageUri.getScheme();
                        String fileName="";
                        if (scheme.equals("file")) {
                            fileName = selectedImageUri.getLastPathSegment();
                        }
                        else if (scheme.equals("content")) {
                            String[] proj = { MediaStore.Images.Media.TITLE };
                            Cursor cursor = this.getContentResolver().query(selectedImageUri, null, null, null, null);
                            if (cursor != null && cursor.getCount() != 0) {
                                //int columnIndex = cursor.getColumnIndexOrThrow(MediaStore.Images.Media.TITLE);
                                cursor.moveToFirst();
                                fileName = cursor.getString(cursor.getColumnIndex(OpenableColumns.DISPLAY_NAME));
                            }
                            if (cursor != null) {
                                cursor.close();
                            }
                        }
                        jsonObject.put("fileName", fileName);
                        InputStream fis = getContentResolver().openInputStream(selectedImageUri);
                        //FileInputStream fis= new FileInputStream(fimage);

                        byte[] image= new byte[fis.available()];
                        fis.read(image);

                        jsonObject.put("image", new String(Base64.encode(image, Base64.DEFAULT)));
                        global.getCallbackContext().success(jsonObject);

                    } catch (Throwable e) {
                        JSONObject jsonerror = new JSONObject();
                        try {
                            jsonObject.put("messageerror", Base64.encode("3001 - Ocurrio un error al establecer la imagen".getBytes(), Base64.DEFAULT));
                            global.getCallbackContext().error(jsonObject);
                        } catch (JSONException e1) {
                            e1.printStackTrace();
                        }
                        e.printStackTrace();
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
        global.setmIsInForegroundMode(false);
        // Logs 'app deactivate' App Event.
        AppEventsLogger.deactivateApp(this);
    }

    public String capitalize(String string){
        char[] array = string.toCharArray();

        array[0] = Character.toUpperCase(array[0]);

        return new String(array);
    }
    public void deleteOldFiles(){
        File dir = new File(Environment.getExternalStorageDirectory()+MainActivity.appRootFolder);
        if (!dir.exists()){
            return;
        }
        deleteAFile(dir);
    }

    public void deleteAFile(File dir){
        if (dir.isDirectory())
        {
            String[] children = dir.list();
            for (int i = 0; i < children.length; i++)
            {
                File file= new File(dir, children[i]);
                if (file.isDirectory()){
                    deleteAFile(file);
                }

                    file.delete();
            }
        }
    }

    @Override
    protected void onNewIntent(final Intent intent) {
        super.onNewIntent(intent);
        gamesReturn( intent);
    }

    protected boolean gamesReturn (final Intent intent) {
        super.onNewIntent(intent);
        preventToLoad=false;
        global=Global.getInstance();
        if (intent.getExtras()==null){
            return true;
        }
        Log.d("ANALU", "Entre");

        if (intent.getExtras().containsKey(NOTIFICATION_INTENT)){
            final File file = new File(appPath(), "redirectToAndroid.html");
            Uri uri = Uri.fromFile(file);
            loadUrl(uri.toString() +"?url=" +"index.html#/AlertsDetail/-1/"+ intent.getExtras().getInt(POST_ID));

            return false;
        }

        String gameArguments=intent.getExtras().getString("game_arguments");

        if (gameArguments==null){
            Log.d("Avatar", "No hay argumentos");
            return true;
        }
        Log.d("ANALU", gameArguments);
        JSONObject jsonObject=null ;
        try {
            jsonObject=new JSONObject(gameArguments);
            if (jsonObject.getString("actividad").equals("Mi Avatar") || jsonObject.getString("actividad").equals("Reto múltiple")) {
                String userId = jsonObject.getString(jsonObject.has("userId") ? "userId" : "userid");
                String imagepath ="avatar_"+ userId +".png"; //searchForAvatar(avatarFolder);
                jsonObject.put("pathImagen", imagepath);
            }
            if (global.getCallbackContextGames() != null){


                final JSONObject finalJsonObject = jsonObject;
                handler.post(new Runnable(){
                    public void run(){
                       /* PluginResult result = new PluginResult(PluginResult.Status.OK, finalJsonObject);
                        result.setKeepCallback(true);
                        global.getCallbackContext().sendPluginResult(result);*/

                        //global.getCallbackContext().success(finalJsonObject);
                        Log.d("SystemWebChromeClient",finalJsonObject.toString());
                        global.getCallbackContextGames().success(finalJsonObject);


                    }
                }
                );
            }
            else{
                preventToLoad=true;
                final File file = new File(appFolder, "redirectToAndroid.html");
                //final File file = new File(appFolder, "index.html");
                Uri uri = Uri.fromFile(file);
                global.setMainActivity(this);
                String url = "index.html#/";
                if (jsonObject.getString("actividad").equals("Reto múltiple")){
                    global.setRetosMultiplesIntent(intent);
                    url += "ZonaDeVuelo/Conocete/RetoMultiple/1039/1";
                }else if (jsonObject.getString("actividad").equals("Tú eliges")) {
                    global.setTuEligesIntent(intent);
                    url += "ZonaDeNavegacion/TuEliges/TuEliges/2012/1";
                } else if (jsonObject.getString("actividad").equals("Proyecta tu vida")){
                    global.setProyectaTuVidaIntent(intent);
                    url += "ZonaDeNavegacion/ProyectaTuVida/MapaDeVida/2017/1";
                }else if (jsonObject.getString("actividad").equals("Multiplica tu dinero")){
                    global.setMultiplicaTuDineroIntent(intent);
                    url += "ZonaDeAterrizaje/EducacionFinanciera/MultiplicaTuDinero/3302/1";
                }else if (jsonObject.getString("actividad").equals("Fábrica de emprendimiento")){
                    global.setFabricaDeEmprendimientoIntent(intent);
                    url += "ZonaDeAterrizaje/MapaDelEmprendedor/MapaDelEmprendedor/3402/1";
                }else if(jsonObject.getString("actividad").equals("Mi Avatar")) {
                    String userId = jsonObject.getString("userId");
                    url += (getAvatarCheckpoint().equals("Tutorial") ? "Tutorial/1" : "Perfil/" + userId + "/1" );
                    Log.d("Avatar", "Intento reanudar avatar con esta url " +url);
                    global.setMiAvatarIntent(intent);
                }else{
                    Toast.makeText(this, "Se perdió la conexión con el juego", Toast.LENGTH_SHORT).show();
                }
                Log.d("ANALU", uri.toString() + "?url=" + url + "&imacellphone=true");
                loadUrl(uri.toString() + "?url=" + url + "&imacellphone=true");
            }

        } catch (JSONException e) {
            e.printStackTrace();
            Toast.makeText(this, "4001 - Ocurrio un error", Toast.LENGTH_SHORT).show();
            return true;
        }
        return false;
    }

    public String searchForAvatar(String subfolder){
        File dir = new File(appFolder+"/"+subfolder);
        File selectedAvatar=null;
        if (dir.isDirectory())
        {
            long lastModified=0;
            String[] children = dir.list();
            for (int i = 0; i < children.length; i++)
            {
                if(children[i].endsWith(".png")){
                    File fAvatar=   new File(dir, children[i]);
                    if (fAvatar.lastModified()>lastModified){
                        if (selectedAvatar!=null){
                            selectedAvatar.delete();
                        }
                        selectedAvatar=fAvatar;
                        lastModified=fAvatar.lastModified();
                    }
                }
            }
        }
        if (selectedAvatar!=null){
            return Uri.parse(selectedAvatar.getAbsolutePath()).getLastPathSegment();
        }

        return "";
    }

    /*public void sendByMail(String image, String fileName, String mailsubject){
        byte [] imagebytes= Base64.decode(image.getBytes(), Base64.DEFAULT);

        FileOutputStream fos = null;
        File tempFBDataFile  = new File(*//*getFilesDir()*//*getExternalCacheDir(),fileName);
        try {
            fos  = new FileOutputStream(tempFBDataFile);//openFileOutput(getExternalCacheDir()+"/"+fileName, Context.MODE_WORLD_READABLE);
            fos.write(imagebytes,0,imagebytes.length);
            fos.flush();
            fos.close();
        } catch (Throwable ioe) {
            ioe.printStackTrace();
        }
        finally {
            if (fos != null)try {fos.close();} catch (Throwable ie) {ie.printStackTrace();}
        }

        Intent emailClient = new Intent(Intent.ACTION_SEND);
        emailClient.setType("message/rfc822");
        //emailClient.putExtra(Intent.EXTRA_EMAIL, new String[]{data.getCredentials().getOrganizationEmail()});
        emailClient.putExtra(Intent.EXTRA_SUBJECT, mailsubject);
        emailClient.putExtra(Intent.EXTRA_TEXT, "");
        emailClient.putExtra(Intent.EXTRA_STREAM, Uri.fromFile(tempFBDataFile));//attachment
        Intent emailChooser = Intent.createChooser(emailClient, "Selecciona una aplicación de email");
        startActivity(emailChooser);
        tempFBDataFile.deleteOnExit();
    }*/

    public void sendSeveralByMail(List<String> images, List<String>  fileNames, String mailsubject){
        ArrayList<Uri> uris = new ArrayList<Uri>();
        for (int i=0; i< images.size();i++){
            String image= images.get(i);
            String fileName= fileNames.get(i);
            byte [] imagebytes= Base64.decode(image.getBytes(), Base64.DEFAULT);

            FileOutputStream fos = null;
            File tempFBDataFile  = new File(/*getFilesDir()*/getExternalCacheDir(),fileName);
            try {
                fos  = new FileOutputStream(tempFBDataFile);//openFileOutput(getExternalCacheDir()+"/"+fileName, Context.MODE_WORLD_READABLE);
                fos.write(imagebytes,0,imagebytes.length);
                fos.flush();
                fos.close();
                uris.add(Uri.fromFile(tempFBDataFile));
                tempFBDataFile.deleteOnExit();
            } catch (Throwable ioe) {
                ioe.printStackTrace();
            }
            finally {
                if (fos != null)try {fos.close();} catch (Throwable ie) {ie.printStackTrace();}
            }
        }


        Intent emailClient = new Intent(Intent.ACTION_SEND_MULTIPLE);
        emailClient.setType("message/rfc822");
        //emailClient.putExtra(Intent.EXTRA_EMAIL, new String[]{data.getCredentials().getOrganizationEmail()});
        emailClient.putExtra(Intent.EXTRA_SUBJECT, mailsubject);
        emailClient.putExtra(Intent.EXTRA_TEXT, "");
       // emailClient.putExtra(Intent.EXTRA_STREAM, Uri.fromFile(tempFBDataFile));//attachment
        emailClient.putParcelableArrayListExtra(Intent.EXTRA_STREAM, uris);
        Intent emailChooser = Intent.createChooser(emailClient, "Selecciona una aplicación de email");
        startActivity(emailChooser);
        //tempFBDataFile.deleteOnExit();
    }


    public void share(List<String> images){

        ArrayList<Uri> uris = new ArrayList<Uri>();
        int i=0;
        for (String image:images) {
            byte [] imagebytes= Base64.decode(image.getBytes(), Base64.DEFAULT);
            String fileName="attachment"+i+".png";

            FileOutputStream fos = null;
            File tempFBDataFile  = new File(/*getFilesDir()*/getExternalCacheDir(),fileName);
            try {
                fos  = new FileOutputStream(tempFBDataFile);//openFileOutput(getExternalCacheDir()+"/"+fileName, Context.MODE_WORLD_READABLE);
                fos.write(imagebytes, 0, imagebytes.length);
                fos.flush();
                fos.close();

                Uri uri = Uri.fromFile(tempFBDataFile);
                String path = uri.getPath();
                File imageFile = new File(path);
                uri = getImageContentUri(imageFile);

                uris.add(uri);
                tempFBDataFile.deleteOnExit();

            } catch (Throwable ioe) {
                ioe.printStackTrace();
            }
            finally {
                if (fos != null)try {fos.close();} catch (Throwable ie) {ie.printStackTrace();}
            }
            i++;
        }

        Intent intent = new Intent();
        intent.setAction(Intent.ACTION_SEND_MULTIPLE);
        //intent.setType("image/*");
        intent.setType("image/*");
        //intent.putExtra(Intent.EXTRA_TEXT, "Holilla");
        //intent.putExtra(Intent.EXTRA_STREAM, Uri.fromFile(tempFBDataFile));
        intent.putParcelableArrayListExtra(Intent.EXTRA_STREAM, uris);
        startActivity(Intent.createChooser(intent, "Seleccione una aplicación"));


    }

    public void download(List<String> images){
        int i=0;
        createDownloadingNotification();
        for (String image:images) {
            byte [] imagebytes= Base64.decode(image.getBytes(), Base64.DEFAULT);

            String fileName="mision incluso +"+getDate("ymdhms")+i+"+.png";

            FileOutputStream fos = null;
            File tempFBDataFile  = new File(Environment.getExternalStoragePublicDirectory(
                    Environment.DIRECTORY_PICTURES) +"/Mision Incluso/");

            tempFBDataFile.mkdir();

            tempFBDataFile  = new File(Environment.getExternalStoragePublicDirectory(
                    Environment.DIRECTORY_PICTURES) +"/Mision Incluso/"+fileName);

            if (tempFBDataFile.exists()){
                tempFBDataFile.delete();
            }

            try {
                fos  = new FileOutputStream(tempFBDataFile);//openFileOutput(getExternalCacheDir()+"/"+fileName, Context.MODE_WORLD_READABLE);
                fos.write(imagebytes,0,imagebytes.length);
                fos.flush();
                fos.close();
                sendBroadcast(new Intent(Intent.ACTION_MEDIA_SCANNER_SCAN_FILE, Uri.fromFile(tempFBDataFile)));
                createDownloadNotification(Uri.fromFile(tempFBDataFile));
            } catch (Throwable ioe) {
                ioe.printStackTrace();
            }
            finally {
                if (fos != null)try {fos.close();} catch (Throwable ie) {ie.printStackTrace();}
            }
            i++;
        }

    }

    public static String getDate(String ls_format){
        String ls_folio="", ls_letraAct;
        Calendar c = Calendar.getInstance();
        for (int i=0; i<ls_format.length();i++){
            ls_letraAct=ls_format.substring(i, i+1)/*.toLowerCase()*/;
            if (ls_letraAct.equals("y")){
                ls_folio+= fillString(String.valueOf(c.get(Calendar.YEAR)), "0", 4, true);
            }
            else if (ls_letraAct.equals("Y")){
                ls_folio+= fillString(String.valueOf(c.get(Calendar.YEAR)).substring(2), "0", 2, true);
            }
            else if (ls_letraAct.equals("m")){
                ls_folio+= fillString(String.valueOf(c.get(Calendar.MONTH) + 1), "0", 2, true);
            }
            else if (ls_letraAct.equals("d")){
                ls_folio+= fillString(String.valueOf(c.get(Calendar.DAY_OF_MONTH)), "0", 2, true);
            }
            else if (ls_letraAct.equals("h")){
                ls_folio+= fillString(String.valueOf(c.get(Calendar.HOUR_OF_DAY)), "0", 2, true);
            }
            else if (ls_letraAct.equals("i")){
                ls_folio+= fillString(String.valueOf(c.get(Calendar.MINUTE)), "0", 2, true);
            }
            else if (ls_letraAct.equals("s")){
                ls_folio+= fillString(String.valueOf(c.get(Calendar.SECOND)), "0", 2, true);
            }
            else
            {
                ls_folio +=ls_letraAct;
            }


        }
        return ls_folio;
    }

    public static String fillString(String text, String fill, int times, boolean place) {
        String ls_final = text;
        int li_restantes;

        if (times < text.length()) {
            return text.substring(0, times);
        }

        li_restantes = times - text.length();


        for (int i = 0; i < li_restantes; i++) {
            if (place)
                ls_final = fill + ls_final;
            else
                ls_final = ls_final + fill;
        }
        return ls_final;
    }

    public Uri getImageContentUri(File imageFile) {
        String filePath = imageFile.getAbsolutePath();
        Cursor cursor = getContentResolver().query(
                MediaStore.Images.Media.EXTERNAL_CONTENT_URI,
                new String[] { MediaStore.Images.Media._ID },
                MediaStore.Images.Media.DATA + "=? ",
                new String[] { filePath }, null);
        if (cursor != null && cursor.moveToFirst()) {
            int id = cursor.getInt(cursor
                    .getColumnIndex(MediaStore.MediaColumns._ID));
            //Uri baseUri = Uri.parse("content://media/external/images/media");
            return Uri.withAppendedPath(MediaStore.Images.Media.EXTERNAL_CONTENT_URI, Integer.toString(id));
        } else {
            if (imageFile.exists()) {
                ContentValues values = new ContentValues();
                values.put(MediaStore.Images.Media.DATA, filePath);
                return getContentResolver().insert(
                        MediaStore.Images.Media.EXTERNAL_CONTENT_URI, values);
            } else {
                return null;
            }
        }
    }
    public void openDatePickerDialog(String date)
    {
        int mYear=0, mMonth=0, mDay=0;
        if (date.equals("")){
            Calendar c = Calendar.getInstance();
            mYear = c.get(Calendar.YEAR);
            mMonth = c.get(Calendar.MONTH);
            mDay = c.get(Calendar.DAY_OF_MONTH);
        }
        else{
            String[] values=date.split("/");
            mYear = Integer.parseInt(values[2]);
            mMonth = Integer.parseInt(values[1])-1;
            mDay = Integer.parseInt(values[0]);
        }


        //updateDisplay();
        DatePickerDialog dp = new DatePickerDialog(this,
                this,
                mYear, mMonth, mDay);
        dp.show();
    }

    @Override
    public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
        global.getCallbackContext().success(fillString(String.valueOf(dayOfMonth), "0", 2, true) + "/" + fillString(String.valueOf(monthOfYear + 1), "0", 2, true) + "/" + String.valueOf(year));
    }

    public void restart(){
        Intent i = getBaseContext().getPackageManager()
                .getLaunchIntentForPackage( getBaseContext().getPackageName() );
        //i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        i.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        i.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TASK);
        i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        startActivity(i);
        setResult(Activity.RESULT_CANCELED);
        finish();
        System.exit(0);
    }

    public void createDownloadNotification(Uri uri){


        Intent intent = new Intent(Intent.ACTION_VIEW/*, uri*/);
        intent.setDataAndType(uri, "image/*");
        PendingIntent pIntent = PendingIntent.getActivity(this, 0, intent, 0);


        NotificationCompat.Builder mBuilder =
                new NotificationCompat.Builder(this)
                        .setSmallIcon(android.R.drawable.stat_sys_download_done)
                        .setContentTitle("Mision Incluso")
                        .setContentText("Imagen descargada").setContentIntent(pIntent);

                    NotificationManager mNotificationManager =
                            (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

        Notification noti =mBuilder.build();

        noti.flags = Notification.FLAG_AUTO_CANCEL;
        mNotificationManager.notify(DOWNLOAD_NOTIFICATION, noti);
        //mNotificationManager.notify(DOWNLOADING_NOTIFICATION, noti);
        mNotificationManager.cancel(DOWNLOADING_NOTIFICATION);
    }

    public void createDownloadingNotification(){

        NotificationCompat.Builder mBuilder =
                new NotificationCompat.Builder(this)
                        .setSmallIcon(android.R.drawable.stat_sys_download)
                        .setContentTitle("Mision Incluso")
                        .setContentText("Descargando Imagen");

        NotificationManager mNotificationManager =
                (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        Notification noti =mBuilder.build();

        noti.flags |= Notification.FLAG_NO_CLEAR;
        mNotificationManager.notify(DOWNLOADING_NOTIFICATION, noti);
    }

    public void setAvatarCheckpoint(String type){

            String fileName="avatar.dat";

            FileOutputStream fos = null;
            File tempFBDataFile  = new File(appPath(), fileName);

            //tempFBDataFile.mkdir();
            if (tempFBDataFile.exists()){
                tempFBDataFile.delete();
            }

            try {
                fos  = new FileOutputStream(tempFBDataFile);//openFileOutput(getExternalCacheDir()+"/"+fileName, Context.MODE_WORLD_READABLE);
                fos.write(type.getBytes(),0,type.getBytes().length);
                fos.flush();
                fos.close();
            } catch (Throwable ioe) {
                ioe.printStackTrace();
            }
            finally {
                if (fos != null)try {fos.close();} catch (Throwable ie) {ie.printStackTrace();}
            }


    }

    public String getAvatarCheckpoint(){
        final File file = new File(appPath(), "avatar.dat");
        String type="";
        try {
            BufferedReader reader= new BufferedReader(new FileReader(file));
            long bufferedLength=0;
            while (bufferedLength<file.length()){
                type+=Character.toString((char) reader.read());
                bufferedLength++;
            }
        } catch (FileNotFoundException e) {
            e.printStackTrace();
        } catch (IOException e) {
            e.printStackTrace();
        }
        return type;
    }

    public void genericFileSaver(String content){

        String fileName="json"+getDate("ymdhis")+".txt";

        FileOutputStream fos = null;
        File tempFBDataFile  = new File(appPath(), fileName);

        //tempFBDataFile.mkdir();
        if (tempFBDataFile.exists()){
            tempFBDataFile.delete();
        }

        try {
            fos  = new FileOutputStream(tempFBDataFile);//openFileOutput(getExternalCacheDir()+"/"+fileName, Context.MODE_WORLD_READABLE);
            fos.write(content.getBytes(),0,content.getBytes().length);
            fos.flush();
            fos.close();
        } catch (Throwable ioe) {
            ioe.printStackTrace();
        }
        finally {
            if (fos != null)try {fos.close();} catch (Throwable ie) {ie.printStackTrace();}
        }


    }

    /**
     * Check the device to make sure it has the Google Play Services APK. If
     * it doesn't, display a dialog that allows users to download the APK from
     * the Google Play Store or enable it in the device's system settings.
     */
    public boolean checkPlayServices() {
        GoogleApiAvailability apiAvailability = GoogleApiAvailability.getInstance();
        int resultCode = apiAvailability.isGooglePlayServicesAvailable(this);
        if (resultCode != ConnectionResult.SUCCESS) {
            if (apiAvailability.isUserResolvableError(resultCode)) {
                apiAvailability.getErrorDialog(this, resultCode, PLAY_SERVICES_RESOLUTION_REQUEST)
                        .show();
            }
            return false;
        }
        return true;
    }
}
