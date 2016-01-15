package com.definityfirst.incluso.implementations;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.definityfirst.incluso.MainActivity;
import com.definityfirst.incluso.modules.DownloadedFile;
import com.google.gson.Gson;

import org.apache.cordova.CallbackContext;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLConnection;
import java.util.ArrayList;
import java.util.List;
import java.util.zip.ZipEntry;
import java.util.zip.ZipInputStream;

/**
 * Created by humberto.castaneda on 11/06/2015.
 */
public class DownloadGenericFile extends AsyncTask<String, String, String> {


    Context context;

    CallbackContext callbackContext;
    String appFolder;
    List<DownloadedFile> files;

    public DownloadGenericFile(Context context, CallbackContext callbackContext, List<DownloadedFile> files, String appFolder){
        this.context=context;
        this.callbackContext=callbackContext;
        this.files = files;
        this.appFolder= appFolder;
    }

    @Override
    protected String doInBackground(String... f_url) {

        FileOutputStream fos = null;

        for (DownloadedFile file:files) {
            String fileAbsolutePath= appFolder+"/"+file.getPath();
            try {

                URL url = new URL(file.getDownloadLink());
                URLConnection conection = url.openConnection();
                conection.setConnectTimeout(60000);
                conection.connect();

                int lenghtOfFile = conection.getContentLength();
                int responseCode=((HttpURLConnection) conection).getResponseCode();

                if (responseCode<200 && responseCode>=300){
                    finishLoad();
                    return null;
                }


                // download the file
                InputStream input = new BufferedInputStream(url.openStream(),
                        8192);


                File tempFBDataFile  = new File(fileAbsolutePath, file.getName());

                //tempFBDataFile.mkdir();
                if (tempFBDataFile.exists()){
                    tempFBDataFile.delete();
                }


                fos  = new FileOutputStream(tempFBDataFile);//openFileOutput(getExternalCacheDir()+"/"+fileName, Context.MODE_WORLD_READABLE);


                try {

                    long bufferedLength=0;
                    while (bufferedLength<lenghtOfFile){

                        fos.write( input.read());
                        bufferedLength++;
                        fos.flush();
                    }
                } catch (FileNotFoundException e) {
                    file.setSuccess(false);
                    e.printStackTrace();
                } catch (IOException e) {
                    file.setSuccess(false);
                    e.printStackTrace();
                }
                finally {
                    if (fos!=null){
                        fos.close();
                    }

                    if (input !=null){
                        input.close();
                    }


                }
            } catch (Throwable e) {
                e.printStackTrace();
                file.setSuccess(false);
                Log.e("Error: ", e.getMessage());
            }

        }
        finishLoad();
        return null;
    }

    public void finishLoad(){
        try {
            JSONArray jsonArray=new JSONArray(new Gson().toJson(files));
            JSONObject jsonObject = new JSONObject();
            jsonObject.put("success",true);
            jsonObject.put("files", jsonArray);
            callbackContext.success(jsonObject);
        } catch (JSONException e) {
            JSONObject errorObjet= new JSONObject();
            try {
                errorObjet.put("messageerror",e.getMessage());
                errorObjet.put("success",false);
            } catch (JSONException e1) {
            }
            callbackContext.error(errorObjet);
        }

    }


}
