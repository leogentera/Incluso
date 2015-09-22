package com.definityfirst.incluso.modules;

import android.content.Context;
import android.os.AsyncTask;
import android.os.Environment;
import android.util.Base64;
import android.util.Log;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class RestClient  extends AsyncTask<String, String, String> {

    public static final String GET="GET";
    public static final String POST="POST";
    String method=GET;
    String contentType= "application-json";
    String body="";
    int requestCode=0;


    RestClientListener df;
    Context context;

	public RestClient(Context context, RestClientListener df, String method,  String contentType, String body, int requestCode){
       this.context=context;
        this.df=df;
        this.contentType=contentType;
        this.body=body;
        this.method=POST;
        this.requestCode=requestCode;
	}
    /**
     * Before starting background thread Show Progress Bar Dialog
     * */
    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        //showDialog(progress_bar_type);
    }

    /**
     * Downloading file in background thread
     * */
    @Override
    protected String doInBackground(String... f_url) {

        String path=Environment
                .getExternalStorageDirectory().toString();

        StringBuilder sb=null;
        boolean error=false;

        try {
            URL url = new URL(f_url[0]);
            HttpURLConnection conection = (HttpURLConnection) url.openConnection();
            conection.setRequestMethod(method);
            conection.setRequestProperty("Content-Type", contentType);

            conection.setDoInput(true);
            conection.setDoOutput(true);

            if (method.equals(POST)){
                byte[] outputInBytes = body.getBytes("UTF-8");
                OutputStream os = conection.getOutputStream();
                os.write( outputInBytes );
                os.close();
            }
            conection.connect();

            // this will be useful so that you can show a tipical 0-100%
            // progress bar
            int lenghtOfFile = conection.getContentLength();

            InputStream input = null;
            // download the file
            if (conection.getResponseCode()==200){
                input = new BufferedInputStream(/*url.openStream()*/conection.getInputStream());
            }
            else{
                input = new BufferedInputStream(/*url.openStream()*/conection.getErrorStream());
            }

            String line;
            sb = new StringBuilder();
            BufferedReader br = new BufferedReader(new InputStreamReader(input));
            while ((line = br.readLine()) != null) {
                sb.append(line);
            }

            input.close();


        } catch (Throwable e) {
            error=true;
            Log.e("Error: ", e.getMessage());
        }
        String response="";
        if (sb.toString() == null){
           response="";
            if (error){
                response="{\"messageerror\":\""+ Base64.encodeToString("Ocurrio un error, no se puede conectar con el servidor".getBytes(), Base64.DEFAULT)+"\"}";
            }
        }
        else{
            response=sb.toString();
        }
        finishPost(response, requestCode);
        return null;
    }

    /**
     * Updating progress bar
     * */
    protected void onProgressUpdate(String... progress) {
        // setting progress percentage
        //pDialog.setProgress(Integer.parseInt(progress[0]));
    }

    /**
     * After completing background task Dismiss the progress dialog
     * **/
    @Override
    protected void onPostExecute(String file_url) {
        // dismiss the dialog after the file was downloaded
       // dismissDialog(progress_bar_type);

    }

    public void finishPost(String result, int requestCode){
        df.finishPost(result, requestCode);
    }

}