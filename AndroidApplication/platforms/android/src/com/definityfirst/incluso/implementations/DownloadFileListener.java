package com.definityfirst.incluso.implementations;

public interface DownloadFileListener {
	public void loadFinish();

	public void loadFinish(String page);

	public void localLoadFinish(String page);

    public void changeSpinnerText(String text);

	public void finishGotVersion(String version);
	

}
