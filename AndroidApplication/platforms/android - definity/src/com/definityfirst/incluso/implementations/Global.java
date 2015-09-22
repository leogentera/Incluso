package  com.definityfirst.incluso.implementations;

import  com.definityfirst.incluso.MainActivity;

import org.apache.cordova.CallbackContext;

/**
 * Created by humberto.castaneda on 7/30/2015.
 */
public class Global {
    private static Global global = new Global();

    MainActivity activity=null;
    CallbackContext callbackContext;
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
}
