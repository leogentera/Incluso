package  com.definityfirst.incluso.implementations;

import android.content.Intent;

import  com.definityfirst.incluso.MainActivity;

import org.apache.cordova.CallbackContext;

/**
 * Created by humberto.castaneda on 7/30/2015.
 */
public class Global {
    private static Global global = new Global();

    MainActivity activity=null;
    CallbackContext callbackContext;
    Intent retosMultiplesIntent=null;
    Intent tuEligesIntent = null;
    Intent proyectaTuVidaIntent = null;
    private Global(){

    }

    public static Global getInstance(){

        return global;
    }

    public void setMainActivity(MainActivity activity){
        this.activity=activity;
    }

    public MainActivity getMainActivity() {
        return activity;
    }

    public void setCallbackContext(CallbackContext callbackContext) {
        this.callbackContext = callbackContext;
    }

    public CallbackContext getCallbackContext() {
        return callbackContext;
    }

    public Intent getRetosMultiplesIntent() {
        return retosMultiplesIntent;
    }

    public void setRetosMultiplesIntent(Intent retosMultiplesIntent) {
        this.retosMultiplesIntent = retosMultiplesIntent;
    }

    public Intent getTuEligesIntent(){
        return tuEligesIntent;
    }

    public void setTuEligesIntent(Intent tuEligesIntent){
        this.tuEligesIntent = tuEligesIntent;
    }

    public Intent getProyectaTuVidaIntent(){
        return proyectaTuVidaIntent;
    }

    public void setProyectaTuVidaIntent(Intent proyectaTuVidaIntent){
        this.proyectaTuVidaIntent = proyectaTuVidaIntent;
    }
}
