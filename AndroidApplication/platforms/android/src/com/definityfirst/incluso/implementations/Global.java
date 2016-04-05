package  com.definityfirst.incluso.implementations;

import android.content.Intent;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;

import  com.definityfirst.incluso.MainActivity;

import org.apache.cordova.CallbackContext;

/**
 * Created by humberto.castaneda on 7/30/2015.
 */
public class Global {
    private static Global global = new Global();

    MainActivity activity=null;
    CallbackContext callbackContext;
    CallbackContext callbackContextVersion = null;
    CallbackContext callbackContextGames = null;
    Intent retosMultiplesIntent=null;
    Intent tuEligesIntent = null;
    Intent proyectaTuVidaIntent = null;
    Intent multiplicaTuDineroIntent = null;
    Intent fabricaDeEmprendimientoIntent = null;
    Intent miAvatarIntent = null;
    SharedPreferences sharedPreferences;
    boolean stopAvatar=false;

    private boolean mIsInForegroundMode=true;

    private Global(){

    }

    public static Global getInstance(){

        return global;
    }

    public void setMainActivity(MainActivity activity){
        this.activity=activity;
        sharedPreferences= PreferenceManager.getDefaultSharedPreferences(activity);
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

    public Intent getMultiplicaTuDineroIntent(){
        return multiplicaTuDineroIntent;
    }

    public void setMultiplicaTuDineroIntent(Intent multiplicaTuDineroIntent){
        this.multiplicaTuDineroIntent = multiplicaTuDineroIntent;
    }

    public Intent getFabricaDeEmprendimientoIntent(){
        return fabricaDeEmprendimientoIntent;
    }

    public void setFabricaDeEmprendimientoIntent(Intent fabricaDeEmprendimientoIntent){
        this.fabricaDeEmprendimientoIntent = fabricaDeEmprendimientoIntent;
    }

    public Intent getMiAvatarIntent(){
        return miAvatarIntent;
    }

    public void setMiAvatarIntent(Intent miAvatarIntent){
        this.miAvatarIntent = miAvatarIntent;
    }

    public CallbackContext getCallbackContextVersion() {
        return callbackContextVersion;
    }

    public void setCallbackContextVersion(CallbackContext callbackContextVersion) {
        this.callbackContextVersion = callbackContextVersion;
    }

    public SharedPreferences getSharedPreferences() {
        return sharedPreferences;
    }

    public boolean ismIsInForegroundMode() {
        return mIsInForegroundMode;
    }

    public void setmIsInForegroundMode(boolean mIsInForegroundMode) {
        this.mIsInForegroundMode = mIsInForegroundMode;
    }

    public boolean isStopAvatar() {
        return stopAvatar;
    }

    public void setStopAvatar(boolean stopAvatar) {
        this.stopAvatar = stopAvatar;
    }

    public CallbackContext getCallbackContextGames() {
        return callbackContextGames;
    }

    public void setCallbackContextGames(CallbackContext callbackContextGames) {
        this.callbackContextGames = callbackContextGames;
    }
}
