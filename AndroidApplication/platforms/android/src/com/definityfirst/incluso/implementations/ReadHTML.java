package com.definityfirst.incluso.implementations;

import android.content.Context;
import android.os.Environment;
import android.util.Log;

import com.definityfirst.incluso.MainActivity;

import java.io.BufferedInputStream;
import java.io.InputStream;
import java.net.URL;
import java.net.URLConnection;

public class ReadHTML extends DownloadFile {


	public ReadHTML(Context context, DownloadFileListener df, String appFolder){
       super(context, df, appFolder);
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
        String version=((MainActivity)df).getVersion();
        try {

            URL url = new URL(f_url[0]);
            URLConnection conection = url.openConnection();
            conection.setConnectTimeout(60000);
            conection.connect();

            // this will be useful so that you can show a tipical 0-100%
            // progress bar
            int lenghtOfFile = conection.getContentLength();

            // download the file
            InputStream input = new BufferedInputStream(url.openStream(),
                    8192);

            //if (input.available()>0){

            if (lenghtOfFile>0){
                //df.changeSpinnerText("Instalando última versión");

                byte[] version_bytes=new byte[lenghtOfFile];
                input.read(version_bytes);
                version = new String(version_bytes);
            }
            input.close();

        } catch (Throwable e) {
            Log.e("Error: ", e.getMessage());
        }
        df.finishGotVersion(version);
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

}