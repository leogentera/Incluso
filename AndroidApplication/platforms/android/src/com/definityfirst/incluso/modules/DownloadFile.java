package  com.definityfirst.incluso.modules;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.zip.ZipEntry;
import java.util.zip.ZipInputStream;

/**
 * Created by humberto.castaneda on 11/06/2015.
 */
public class DownloadFile  extends AsyncTask<String, String, String> {

    DownloadFileListener df;
    String appFolder;
    Context context;

    String errorPage= "file:///android_asset/www/default.html";

    DownloadFile(Context context, DownloadFileListener df, String appFolder){
        this.context=context;
        this.df=df;
        this.appFolder= appFolder;
    }

    @Override
    protected String doInBackground(String... params) {
        return null;
    }

    public void unZipAndSave(InputStream input, String path) throws IOException {
        int count;
        ZipInputStream zis;
        zis = new ZipInputStream(input);


        ZipEntry ze;
        byte[] buffer = new byte[1024];


        String filename="";

//            File root = new File(path);
//
//            if (!root.exists()){
//            	root.m
//            }

           /* File dir = new File(path);
            if (dir.isDirectory()) {
                    String[] children = dir.list();
                    for (int i = 0; i < children.length; i++) {
                        new File(dir, children[i]).delete();
                    }
                }*/

        while ((ze = zis.getNextEntry()) != null)
        {
            // zapis do souboru
            filename = ze.getName();

            // Need to create directories if not exists, or
            // it will generate an Exception...
            if (ze.isDirectory()) {
                File fmd = new File(path + "/"+ filename);
                fmd.mkdirs();
                continue;
            }else{
                int pathEnding=filename.lastIndexOf("/");
                if (pathEnding >0){
                    File fmd = new File(path + "/"+ filename.substring(0, pathEnding));
                    fmd.mkdirs();
                }
                File currentFile = new File(path + "/"+ filename);
                if (currentFile.exists()){
                    currentFile.delete();
                }
            }

            FileOutputStream fout = new FileOutputStream(path + "/"+ filename);

            // cteni zipu a zapis
            Log.v("Humberto", filename);
            while ((count = zis.read(buffer)) != -1)
            {
                fout.write(buffer, 0, count);
            }

            fout.close();
            zis.closeEntry();
        }

        zis.close();
    }


    public void finishLoad(){
        //final File file = new File(appFolder, "index.html");
        final File file = new File(appFolder, "redirectToAndroid.html");
        if ( file.exists())
            //df.loadFinish("file://" + appFolder+"/index.html");
            df.loadFinish("file://" + appFolder+"/redirectToAndroid.html?url=index.html&imacellphone=true");
        else
            df.loadFinish(errorPage);
    }


}
